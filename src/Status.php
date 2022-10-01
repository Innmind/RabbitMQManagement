<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management;

use Innmind\RabbitMQ\Management\{
    Status\Environment,
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

final class Status
{
    private Server $server;
    private Clock $clock;
    private Environment $environment;
    private Command $command;

    private function __construct(
        Server $server,
        Clock $clock,
        Environment $environment = null,
    ) {
        $this->server = $server;
        $this->clock = $clock;
        $this->environment = $environment ?? Environment\Local::of();
        $this->command = Command::foreground('rabbitmqadmin')
            ->withShortOption('f', 'raw_json')
            ->withArgument('list');
    }

    public static function of(
        Server $server,
        Clock $clock,
        Environment $environment = null,
    ): self {
        return new self($server, $clock, $environment);
    }

    /**
     * @return Set<User>
     */
    public function users(): Set
    {
        /** @var Sequence<array{name: string, password_hash: string, hashing_algorithm: string, tags: string}> */
        $users = $this->list('users');

        return Set::of(
            ...$users
                ->map(static fn($user) => User::of(
                    User\Name::of($user['name']),
                    User\Password::of(
                        $user['password_hash'],
                        $user['hashing_algorithm'],
                    ),
                    ...\explode(',', $user['tags']),
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<VHost>
     */
    public function vhosts(): Set
    {
        /** @var Sequence<array{name: string, messages: 0|positive-int, messages_ready: 0|positive-int, messages_unacknowledged: 0|positive-int, tracing: bool}> */
        $vhosts = $this->list('vhosts');

        return Set::of(
            ...$vhosts
                ->map(static fn($vhost) => VHost::of(
                    VHost\Name::of($vhost['name']),
                    VHost\Messages::of(
                        Count::of($vhost['messages']),
                        Count::of($vhost['messages_ready']),
                        Count::of($vhost['messages_unacknowledged']),
                    ),
                    $vhost['tracing'],
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<Connection>
     */
    public function connections(): Set
    {
        /** @var Sequence<array{name: string, connected_at: int, timeout: 0|positive-int, vhost: string, user: string, protocol: string, auth_mechanism: string, ssl: bool, peer_host: string, peer_port: int, host: string, port: int, node: string, type: 'network'|'direct', state: 'running'|'idle'}> */
        $connections = $this->list('connections');

        return Set::of(
            ...$connections
                ->map(fn($connection) => Connection::of(
                    Connection\Name::of($connection['name']),
                    $this->clock->at(\date(
                        \DateTime::ATOM,
                        (int) \round($connection['connected_at'] / 1000),
                    ))->match(
                        static fn($point) => $point,
                        static fn() => throw new \LogicException,
                    ),
                    Timeout::of($connection['timeout']),
                    VHost\Name::of($connection['vhost']),
                    User\Name::of($connection['user']),
                    Protocol::of($connection['protocol']),
                    AuthenticationMechanism::of($connection['auth_mechanism']),
                    $connection['ssl'],
                    Peer::of(
                        Host::of($connection['peer_host']),
                        Port::of($connection['peer_port']),
                    ),
                    Host::of($connection['host']),
                    Port::of($connection['port']),
                    Node\Name::of($connection['node']),
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

    /**
     * @return Set<Exchange>
     */
    public function exchanges(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, type: 'topic'|'headers'|'direct'|'fanout', durable: bool, auto_delete: bool, internal: bool}> */
        $exchanges = $this->list('exchanges');

        return Set::of(
            ...$exchanges
                ->map(static fn($exchange) => Exchange::of(
                    Exchange\Name::of($exchange['name']),
                    VHost\Name::of($exchange['vhost']),
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

    /**
     * @return Set<Permission>
     */
    public function permissions(): Set
    {
        /** @var Sequence<array{user: string, vhost: string, configure: string, write: string, read: string}> */
        $permissions = $this->list('permissions');

        return Set::of(
            ...$permissions
                ->map(static fn($permission) => Permission::of(
                    User\Name::of($permission['user']),
                    VHost\Name::of($permission['vhost']),
                    $permission['configure'],
                    $permission['write'],
                    $permission['read'],
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<Channel>
     */
    public function channels(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, user: string, number: int, node: string, state: 'running'|'idle', messages_uncommitted: 0|positive-int, messages_unconfirmed: 0|positive-int, messages_unacknowledged: 0|positive-int, consumer_count: 0|positive-int, confirm: bool, transactional: bool, idle_since?: string}> */
        $channels = $this->list('channels');

        return Set::of(
            ...$channels
                ->map(fn($channel) => Channel::of(
                    Channel\Name::of($channel['name']),
                    VHost\Name::of($channel['vhost']),
                    User\Name::of($channel['user']),
                    $channel['number'],
                    Node\Name::of($channel['node']),
                    match ($channel['state']) {
                        'running' => State::running,
                        'idle' => State::idle,
                    },
                    Channel\Messages::of(
                        Count::of($channel['messages_uncommitted']),
                        Count::of($channel['messages_unconfirmed']),
                        Count::of($channel['messages_unacknowledged']),
                    ),
                    Count::of($channel['consumer_count']),
                    $channel['confirm'],
                    $channel['transactional'],
                    Maybe::of($channel['idle_since'] ?? null)->flatMap(
                        $this->clock->at(...),
                    ),
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<Consumer>
     */
    public function consumers(): Set
    {
        /** @var Sequence<array{consumer_tag: string, channel_details: array{name: string, connection_name: string}, queue: array{name: string, vhost: string}, ack_required: bool, exclusive: bool}> */
        $consumers = $this->list('consumers');

        return Set::of(
            ...$consumers
                ->map(static fn($consumer) => Consumer::of(
                    Tag::of($consumer['consumer_tag']),
                    Channel\Name::of($consumer['channel_details']['name']),
                    Identity::of(
                        $consumer['queue']['name'],
                        VHost\Name::of($consumer['queue']['vhost']),
                    ),
                    Connection\Name::of($consumer['channel_details']['connection_name']),
                    $consumer['ack_required'],
                    $consumer['exclusive'],
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<Queue>
     */
    public function queues(): Set
    {
        /** @var Sequence<array{name: string, vhost: string, messages: 0|positive-int, messages_ready: 0|positive-int, messages_unacknowledged: 0|positive-int, idle_since?: string, consumers: 0|positive-int, state: 'running'|'idle', node: string, exclusive: bool, auto_delete: bool, durable: bool}> */
        $queues = $this->list('queues');

        return Set::of(
            ...$queues
                ->map(fn($queue) => Queue::of(
                    Identity::of(
                        $queue['name'],
                        VHost\Name::of($queue['vhost']),
                    ),
                    Queue\Messages::of(
                        Count::of($queue['messages']),
                        Count::of($queue['messages_ready']),
                        Count::of($queue['messages_unacknowledged']),
                    ),
                    Maybe::of($queue['idle_since'] ?? null)->flatMap(
                        $this->clock->at(...),
                    ),
                    Count::of($queue['consumers']),
                    match ($queue['state']) {
                        'running' => State::running,
                        'idle' => State::idle,
                    },
                    Node\Name::of($queue['node']),
                    $queue['exclusive'],
                    $queue['auto_delete'],
                    $queue['durable'],
                ))
                ->toList(),
        );
    }

    /**
     * @return Set<Node>
     */
    public function nodes(): Set
    {
        /** @var Sequence<array{name: string, type: 'disc'|'ram', running: bool}> */
        $nodes = $this->list('nodes');

        return Set::of(
            ...$nodes
                ->map(static fn($node) => Node::of(
                    Node\Name::of($node['name']),
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
