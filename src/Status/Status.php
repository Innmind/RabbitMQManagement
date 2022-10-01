<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status;

use Innmind\RabbitMQ\Management\{
    Status as StatusInterface,
    Model\User,
    Model\VHost,
    Model\Count,
    Model\Connection,
    Model\Connection\Timeout,
    Model\Connection\Protocol,
    Model\Connection\AuthenticationMechanism,
    Model\Connection\Peer,
    Model\State,
    Model\Node,
    Model\Exchange,
    Model\Permission,
    Model\Channel,
    Model\Consumer,
    Model\Consumer\Tag,
    Model\Queue,
    Model\Queue\Identity,
    Exception\ManagementPluginFailedToRun,
};
use Innmind\Server\Control\{
    Server,
    Server\Command,
};
use Innmind\TimeContinuum\Clock;
use Innmind\Url\Authority\{
    Host,
    Port,
};
use Innmind\Immutable\{
    Set,
    Sequence,
    Maybe,
};

final class Status implements StatusInterface
{
    private Server $server;
    private Clock $clock;
    private Environment $environment;
    private Command $command;

    public function __construct(
        Server $server,
        Clock $clock,
        Environment $environment = null,
    ) {
        $this->server = $server;
        $this->clock = $clock;
        $this->environment = $environment ?? new Environment\Local;
        $this->command = Command::foreground('rabbitmqadmin')
            ->withShortOption('f', 'raw_json')
            ->withArgument('list');
    }

    public function users(): Set
    {
        /** @var Sequence<array{name: string, password_hash: string, hashing_algorithm: string, tags: string}> */
        $users = $this->list('users');

        return Set::of(
            ...$users
                ->map(static fn($user) => new User(
                    new User\Name($user['name']),
                    new User\Password(
                        $user['password_hash'],
                        $user['hashing_algorithm'],
                    ),
                    ...\explode(',', $user['tags']),
                ))
                ->toList(),
        );
    }

    public function vhosts(): Set
    {
        /** @var Sequence<array{name: string, messages: int, messages_ready: int, messages_unacknowledged: int, tracing: bool}> */
        $vhosts = $this->list('vhosts');

        return Set::of(
            ...$vhosts
                ->map(static fn($vhost) => new VHost(
                    new VHost\Name($vhost['name']),
                    new VHost\Messages(
                        new Count($vhost['messages']),
                        new Count($vhost['messages_ready']),
                        new Count($vhost['messages_unacknowledged']),
                    ),
                    $vhost['tracing'],
                ))
                ->toList(),
        );
    }

    public function connections(): Set
    {
        /** @var Sequence<array{name: string, connected_at: int, timeout: int, vhost: string, user: string, protocol: string, auth_mechanism: string, ssl: bool, peer_host: string, peer_port: int, host: string, port: int, node: string, type: 'network'|'direct', state: 'running'|'idle'}> */
        $connections = $this->list('connections');

        return Set::of(
            ...$connections
                ->map(fn($connection) => new Connection(
                    new Connection\Name($connection['name']),
                    $this->clock->at(\date(
                        \DateTime::ATOM,
                        (int) \round($connection['connected_at'] / 1000),
                    ))->match(
                        static fn($point) => $point,
                        static fn() => throw new \LogicException,
                    ),
                    new Timeout($connection['timeout']),
                    new VHost\Name($connection['vhost']),
                    new User\Name($connection['user']),
                    Protocol::of($connection['protocol']),
                    AuthenticationMechanism::of($connection['auth_mechanism']),
                    $connection['ssl'],
                    new Peer(
                        Host::of($connection['peer_host']),
                        Port::of($connection['peer_port']),
                    ),
                    Host::of($connection['host']),
                    Port::of($connection['port']),
                    new Node\Name($connection['node']),
                    match ($connection['type']) {
                        'network' => Connection\Type::network,
                        'direct' => Connection\Type::direct,
                    },
                    match ($connection['state']) {
                        'running' => State::running,
                        'idle' => State::idle,
                    },
                ))
                ->toList(),
        );
    }

    public function exchanges(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, type: 'topic'|'headers'|'direct'|'fanout', durable: bool, auto_delete: bool, internal: bool}> */
        $exchanges = $this->list('exchanges');

        return Set::of(
            ...$exchanges
                ->map(static fn($exchange) => new Exchange(
                    new Exchange\Name($exchange['name']),
                    new VHost\Name($exchange['vhost']),
                    match ($exchange['type']) {
                        'topic' => Exchange\Type::topic,
                        'headers' => Exchange\Type::headers,
                        'direct' => Exchange\Type::direct,
                        'fanout' => Exchange\Type::fanout,
                    },
                    $exchange['durable'],
                    $exchange['auto_delete'],
                    $exchange['internal'],
                ))
                ->toList(),
        );
    }

    public function permissions(): Set
    {
        /** @var Sequence<array{user: string, vhost: string, configure: string, write: string, read: string}> */
        $permissions = $this->list('permissions');

        return Set::of(
            ...$permissions
                ->map(static fn($permission) => new Permission(
                    new User\Name($permission['user']),
                    new VHost\Name($permission['vhost']),
                    $permission['configure'],
                    $permission['write'],
                    $permission['read'],
                ))
                ->toList(),
        );
    }

    public function channels(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, user: string, number: int, node: string, state: 'running'|'idle', messages_uncommitted: int, messages_unconfirmed: int, messages_unacknowledged: int, consumer_count: 1, confirm: bool, transactional: bool, idle_since?: string}> */
        $channels = $this->list('channels');

        return Set::of(
            ...$channels
                ->map(fn($channel) => new Channel(
                    new Channel\Name($channel['name']),
                    new VHost\Name($channel['vhost']),
                    new User\Name($channel['user']),
                    $channel['number'],
                    new Node\Name($channel['node']),
                    match ($channel['state']) {
                        'running' => State::running,
                        'idle' => State::idle,
                    },
                    new Channel\Messages(
                        new Count($channel['messages_uncommitted']),
                        new Count($channel['messages_unconfirmed']),
                        new Count($channel['messages_unacknowledged']),
                    ),
                    new Count($channel['consumer_count']),
                    $channel['confirm'],
                    $channel['transactional'],
                    Maybe::of($channel['idle_since'] ?? null)->flatMap(
                        $this->clock->at(...),
                    ),
                ))
                ->toList(),
        );
    }

    public function consumers(): Set
    {
        /** @var Sequence<array{consumer_tag: string, channel_details: array{name: string, connection_name: string}, queue: array{name: string, vhost: string}, ack_required: bool, exclusive: bool}> */
        $consumers = $this->list('consumers');

        return Set::of(
            ...$consumers
                ->map(static fn($consumer) => new Consumer(
                    new Tag($consumer['consumer_tag']),
                    new Channel\Name($consumer['channel_details']['name']),
                    new Identity(
                        $consumer['queue']['name'],
                        new VHost\Name($consumer['queue']['vhost']),
                    ),
                    new Connection\Name($consumer['channel_details']['connection_name']),
                    $consumer['ack_required'],
                    $consumer['exclusive'],
                ))
                ->toList(),
        );
    }

    public function queues(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, messages: int, messages_ready: int, messages_unacknowledged: int, idle_since?: string, consumers: int, state: 'running'|'idle', node: string, exclusive: bool, auto_delete: bool, durable: bool}> */
        $queues = $this->list('queues');

        return Set::of(
            ...$queues
                ->map(fn($queue) => new Queue(
                    new Identity(
                        $queue['name'],
                        new VHost\Name($queue['vhost']),
                    ),
                    new Queue\Messages(
                        new Count($queue['messages']),
                        new Count($queue['messages_ready']),
                        new Count($queue['messages_unacknowledged']),
                    ),
                    Maybe::of($queue['idle_since'] ?? null)->flatMap(
                        $this->clock->at(...),
                    ),
                    new Count($queue['consumers']),
                    match ($queue['state']) {
                        'running' => State::running,
                        'idle' => State::idle,
                    },
                    new Node\Name($queue['node']),
                    $queue['exclusive'],
                    $queue['auto_delete'],
                    $queue['durable'],
                ))
                ->toList(),
        );
    }

    public function nodes(): Set
    {
        /** @var Sequence<array{name: string, type: 'disc'|'ram', running: bool}> */
        $nodes = $this->list('nodes');

        return Set::of(
            ...$nodes
                ->map(static fn($node) => new Node(
                    new Node\Name($node['name']),
                    match ($node['type']) {
                        'disc' => Node\Type::disc,
                        'ram' => Node\Type::ram,
                    },
                    $node['running'],
                ))
                ->toList(),
        );
    }

    /**
     * @return Sequence<array>
     */
    private function list(string $element): Sequence
    {
        $process = $this
            ->server
            ->processes()
            ->execute(
                ($this->environment)(
                    $this->command->withArgument($element),
                ),
            );
        $successful = $process->wait()->match(
            static fn() => true,
            static fn() => false,
        );

        if (!$successful) {
            throw new ManagementPluginFailedToRun;
        }

        /** @var list<array> */
        $elements = \json_decode($process->output()->toString(), true);

        /** @var Sequence<array> */
        return Sequence::mixed(...$elements);
    }
}
