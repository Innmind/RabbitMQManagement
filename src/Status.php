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
};
use Innmind\Server\Control\{
    Server,
    Server\Command,
};
use Innmind\TimeContinuum\{
    Clock,
    PointInTime,
    Format,
};
use Innmind\Url\Authority\{
    Host,
    Port,
};
use Innmind\Validation\Is;
use Innmind\Immutable\{
    Sequence,
    Maybe,
    Monoid\Concat,
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
        ?Environment $environment = null,
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
        ?Environment $environment = null,
    ): self {
        return new self($server, $clock, $environment);
    }

    /**
     * @return Sequence<User>
     */
    public function users(): Sequence
    {
        /** @var Sequence<array{name: string, password_hash: string, hashing_algorithm: string, tags: string}> */
        $users = $this->list('users');

        return $users->map(static fn($user) => User::of(
            User\Name::of($user['name']),
            User\Password::of(
                $user['password_hash'],
                $user['hashing_algorithm'],
            ),
            ...\explode(',', $user['tags']),
        ));
    }

    /**
     * @return Sequence<VHost>
     */
    public function vhosts(): Sequence
    {
        /** @var Sequence<array{name: string, messages: 0|positive-int, messages_ready: 0|positive-int, messages_unacknowledged: 0|positive-int, tracing: bool}> */
        $vhosts = $this->list('vhosts');

        return $vhosts->map(static fn($vhost) => VHost::of(
            VHost\Name::of($vhost['name']),
            VHost\Messages::of(
                Count::of($vhost['messages']),
                Count::of($vhost['messages_ready']),
                Count::of($vhost['messages_unacknowledged']),
            ),
            $vhost['tracing'],
        ));
    }

    /**
     * @return Sequence<Connection>
     */
    public function connections(): Sequence
    {
        /** @var Sequence<array{name: string, connected_at: int, timeout: 0|positive-int, vhost: string, user: string, protocol: string, auth_mechanism: string, ssl: bool, peer_host: string, peer_port: int, host: string, port: int, node: string, type: 'network'|'direct', state: 'running'|'idle'}> */
        $connections = $this->list('connections');

        /** @psalm-suppress ArgumentTypeCoercion */
        return $connections
            ->map(
                fn($connection) => Maybe::all(
                    $this
                        ->clock
                        ->at(
                            \date(
                                \DateTime::ATOM,
                                (int) \round($connection['connected_at'] / 1000),
                            ),
                            Format::iso8601(),
                        ),
                    Node\Name::maybe($connection['node']),
                )
                    ->map(static fn(PointInTime $connectedAt, Node\Name $node) => Connection::of(
                        Connection\Name::of($connection['name']),
                        $connectedAt,
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
                        $node,
                        match ($connection['type']) {
                            'network' => Connection\Type::network,
                            'direct' => Connection\Type::direct,
                        },
                        match ($connection['state']) {
                            'running' => State::running,
                            'idle' => State::idle,
                        },
                    )),
            )
            ->flatMap(static fn($maybe) => $maybe->toSequence());
    }

    /**
     * @return Sequence<Exchange>
     */
    public function exchanges(): Sequence
    {
        /** @var Sequence<array{name: string, vhost: string, type: 'topic'|'headers'|'direct'|'fanout', durable: bool, auto_delete: bool, internal: bool}> */
        $exchanges = $this->list('exchanges');

        return $exchanges->map(static fn($exchange) => Exchange::of(
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
        ));
    }

    /**
     * @return Sequence<Permission>
     */
    public function permissions(): Sequence
    {
        /** @var Sequence<array{user: string, vhost: string, configure: string, write: string, read: string}> */
        $permissions = $this->list('permissions');

        return $permissions->map(static fn($permission) => Permission::of(
            User\Name::of($permission['user']),
            VHost\Name::of($permission['vhost']),
            $permission['configure'],
            $permission['write'],
            $permission['read'],
        ));
    }

    /**
     * @return Sequence<Channel>
     */
    public function channels(): Sequence
    {
        /** @var Sequence<array{name: string, vhost: string, user: string, number: int, node: string, state: 'running'|'idle', messages_uncommitted: 0|positive-int, messages_unconfirmed: 0|positive-int, messages_unacknowledged: 0|positive-int, consumer_count: 0|positive-int, confirm: bool, transactional: bool, idle_since?: non-empty-string}> */
        $channels = $this->list('channels');

        return $channels
            ->map(fn($channel) => Node\Name::maybe($channel['node'])->map(
                fn($node) => Channel::of(
                    Channel\Name::of($channel['name']),
                    VHost\Name::of($channel['vhost']),
                    User\Name::of($channel['user']),
                    $channel['number'],
                    $node,
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
                        $this
                            ->clock
                            ->ofFormat(Format::of('Y-m-d G:i:s'))
                            ->at(...),
                    ),
                ),
            ))
            ->flatMap(static fn($maybe) => $maybe->toSequence());
    }

    /**
     * @return Sequence<Consumer>
     */
    public function consumers(): Sequence
    {
        /** @var Sequence<array{consumer_tag: string, channel_details: array{name: string, connection_name: string}, queue: array{name: string, vhost: string}, ack_required: bool, exclusive: bool}> */
        $consumers = $this->list('consumers');

        return $consumers->map(static fn($consumer) => Consumer::of(
            Tag::of($consumer['consumer_tag']),
            Channel\Name::of($consumer['channel_details']['name']),
            Identity::of(
                $consumer['queue']['name'],
                VHost\Name::of($consumer['queue']['vhost']),
            ),
            Connection\Name::of($consumer['channel_details']['connection_name']),
            $consumer['ack_required'],
            $consumer['exclusive'],
        ));
    }

    /**
     * @return Sequence<Queue>
     */
    public function queues(): Sequence
    {
        /** @var Sequence<array{name: string, vhost: string, messages: 0|positive-int, messages_ready: 0|positive-int, messages_unacknowledged: 0|positive-int, idle_since?: non-empty-string, consumers: 0|positive-int, state: 'running'|'idle', node: string, exclusive: bool, auto_delete: bool, durable: bool}> */
        $queues = $this->list('queues');

        return $queues->map(fn($queue) => Queue::of(
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
                $this
                    ->clock
                    ->ofFormat(Format::of('Y-m-d G:i:s'))
                    ->at(...),
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
        ));
    }

    /**
     * @return Sequence<Node>
     */
    public function nodes(): Sequence
    {
        /** @var Sequence<array{name: string, type: 'disc'|'ram', running: bool}> */
        $nodes = $this->list('nodes');

        return $nodes
            ->map(static fn($node) => Node\Name::maybe($node['name'])->map(
                static fn($name) => Node::of(
                    $name,
                    match ($node['type']) {
                        'disc' => Node\Type::disc,
                        'ram' => Node\Type::ram,
                    },
                    $node['running'],
                ),
            ))
            ->flatMap(static fn($maybe) => $maybe->toSequence());
    }

    /**
     * @return Sequence<array>
     */
    private function list(string $element): Sequence
    {
        return $this
            ->server
            ->processes()
            ->execute(
                ($this->environment)(
                    $this->command->withArgument($element),
                ),
            )
            ->maybe()
            ->flatMap(static fn($process) => $process->wait()->maybe())
            ->map(
                static fn($success) => $success
                    ->output()
                    ->map(static fn($chunk) => $chunk->data())
                    ->fold(new Concat)
                    ->toString(),
            )
            ->map(static fn($output): mixed => \json_decode($output, true))
            ->keep(Is::list()->asPredicate())
            ->toSequence()
            ->flatMap(static fn($elements) => Sequence::of(...$elements))
            ->keep(Is::array()->asPredicate());
    }
}
