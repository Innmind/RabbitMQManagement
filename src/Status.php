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
    private function __construct(
        private Server $server,
        private Clock $clock,
        private Environment $environment,
        private Command $command,
    ) {
    }

    public static function of(
        Server $server,
        Clock $clock,
        ?Environment $environment = null,
    ): self {
        return new self(
            $server,
            $clock,
            $environment ?? Environment\Local::of(),
            Command::foreground('rabbitmqadmin')
                ->withShortOption('f', 'raw_json')
                ->withArgument('list'),
        );
    }

    /**
     * @return Sequence<User>
     */
    public function users(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('users')
            ->map(
                Is::shape(
                    'name',
                    Is::string()->map(User\Name::of(...)),
                )
                    ->with(
                        'password_hash',
                        Is::string(),
                    )
                    ->with(
                        'hashing_algorithm',
                        Is::string(),
                    )
                    ->with(
                        'tags',
                        Is::string(),
                    )
                    ->map(static fn($shape) => User::of(
                        $shape['name'],
                        User\Password::of(
                            $shape['password_hash'],
                            $shape['hashing_algorithm'],
                        ),
                        ...\explode(',', $shape['tags']),
                    )),
            )
            ->flatMap(static fn($user) => $user->maybe()->toSequence());
    }

    /**
     * @return Sequence<VHost>
     */
    public function vhosts(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('vhosts')
            ->map(
                Is::shape(
                    'name',
                    Is::string()->map(VHost\Name::of(...)),
                )
                    ->with(
                        'messages',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_ready',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_unacknowledged',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'tracing',
                        Is::bool(),
                    )
                    ->map(static fn($shape) => VHost::of(
                        $shape['name'],
                        VHost\Messages::of(
                            $shape['messages'],
                            $shape['messages_ready'],
                            $shape['messages_unacknowledged'],
                        ),
                        $shape['tracing'],
                    )),
            )
            ->flatMap(static fn($vhost) => $vhost->maybe()->toSequence());
    }

    /**
     * @return Sequence<Connection>
     */
    public function connections(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('connections')
            ->map(
                Is::shape(
                    'name',
                    Is::string()->map(Connection\Name::of(...)),
                )
                    ->with(
                        'connected_at',
                        Is::int()
                            ->map(static fn($value) => (string) (int) ($value / 1000))
                            ->map(
                                $this
                                    ->clock
                                    ->ofFormat(Format::of('U'))
                                    ->at(...),
                            )
                            ->and(Is::just()),
                    )
                    ->with(
                        'timeout',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Timeout::of(...)),
                    )
                    ->with(
                        'vhost',
                        Is::string()->map(VHost\Name::of(...)),
                    )
                    ->with(
                        'user',
                        Is::string()->map(User\Name::of(...)),
                    )
                    ->with(
                        'protocol',
                        Is::string()->map(Protocol::of(...)),
                    )
                    ->with(
                        'auth_mechanism',
                        Is::string()->map(AuthenticationMechanism::of(...)),
                    )
                    ->with(
                        'ssl',
                        Is::bool(),
                    )
                    ->with(
                        'peer_host',
                        Is::string()->map(Host::of(...)),
                    )
                    ->with(
                        'peer_port',
                        Is::int()->map(Port::of(...)),
                    )
                    ->with(
                        'host',
                        Is::string()->map(Host::of(...)),
                    )
                    ->with(
                        'port',
                        Is::int()->map(Port::of(...)),
                    )
                    ->with(
                        'node',
                        Is::string()
                            ->map(Node\Name::maybe(...))
                            ->and(Is::just()),
                    )
                    ->with(
                        'type',
                        Is::value('network')
                            ->map(static fn() => Connection\Type::network)
                            ->or(Is::value('direct')->map(
                                static fn() => Connection\Type::direct,
                            )),
                    )
                    ->with(
                        'state',
                        Is::value('running')
                            ->map(static fn() => State::running)
                            ->or(Is::value('idle')->map(
                                static fn() => State::idle,
                            )),
                    )
                    ->map(static fn($shape) => Connection::of(
                        $shape['name'],
                        $shape['connected_at'],
                        $shape['timeout'],
                        $shape['vhost'],
                        $shape['user'],
                        $shape['protocol'],
                        $shape['auth_mechanism'],
                        $shape['ssl'],
                        Peer::of(
                            $shape['peer_host'],
                            $shape['peer_port'],
                        ),
                        $shape['host'],
                        $shape['port'],
                        $shape['node'],
                        $shape['type'],
                        $shape['state'],
                    )),
            )
            ->flatMap(static fn($connection) => $connection->maybe()->toSequence());
    }

    /**
     * @return Sequence<Exchange>
     */
    public function exchanges(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('exchanges')
            ->map(
                Is::shape(
                    'name',
                    Is::string()->map(Exchange\Name::of(...)),
                )
                    ->with(
                        'vhost',
                        Is::string()->map(VHost\Name::of(...)),
                    )
                    ->with(
                        'type',
                        Is::value('topic')
                            ->map(static fn() => Exchange\Type::topic)
                            ->or(Is::value('headers')->map(
                                static fn() => Exchange\Type::headers,
                            ))
                            ->or(Is::value('direct')->map(
                                static fn() => Exchange\Type::direct,
                            ))
                            ->or(Is::value('fanout')->map(
                                static fn() => Exchange\Type::fanout,
                            )),
                    )
                    ->with(
                        'durable',
                        Is::bool(),
                    )
                    ->with(
                        'auto_delete',
                        Is::bool(),
                    )
                    ->with(
                        'internal',
                        Is::bool(),
                    )
                    ->map(static fn($shape) => Exchange::of(
                        $shape['name'],
                        $shape['vhost'],
                        $shape['type'],
                        $shape['durable'],
                        $shape['auto_delete'],
                        $shape['internal'],
                    )),
            )
            ->flatMap(static fn($exchange) => $exchange->maybe()->toSequence());
    }

    /**
     * @return Sequence<Permission>
     */
    public function permissions(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('permissions')
            ->map(
                Is::shape(
                    'user',
                    Is::string()->map(User\Name::of(...)),
                )
                    ->with(
                        'vhost',
                        Is::string()->map(VHost\Name::of(...)),
                    )
                    ->with(
                        'configure',
                        Is::string(),
                    )
                    ->with(
                        'write',
                        Is::string(),
                    )
                    ->with(
                        'read',
                        Is::string(),
                    )
                    ->map(static fn($shape) => Permission::of(
                        $shape['user'],
                        $shape['vhost'],
                        $shape['configure'],
                        $shape['write'],
                        $shape['read'],
                    )),
            )
            ->flatMap(static fn($permission) => $permission->maybe()->toSequence());
    }

    /**
     * @return Sequence<Channel>
     */
    public function channels(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('channels')
            ->map(
                Is::shape(
                    'name',
                    Is::string()->map(Channel\Name::of(...)),
                )
                    ->with(
                        'vhost',
                        Is::string()->map(VHost\Name::of(...)),
                    )
                    ->with(
                        'user',
                        Is::string()->map(User\Name::of(...)),
                    )
                    ->with(
                        'number',
                        Is::int(),
                    )
                    ->with(
                        'node',
                        Is::string()
                            ->map(Node\Name::maybe(...))
                            ->and(Is::just()),
                    )
                    ->with(
                        'state',
                        Is::value('running')
                            ->map(static fn() => State::running)
                            ->or(Is::value('idle')->map(
                                static fn() => State::idle,
                            )),
                    )
                    ->with(
                        'messages_uncommitted',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_unconfirmed',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_unacknowledged',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'consumer_count',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'confirm',
                        Is::bool(),
                    )
                    ->with(
                        'transactional',
                        Is::bool(),
                    )
                    ->optional(
                        'idle_since',
                        Is::string()
                            ->nonEmpty()
                            ->map(
                                $this
                                    ->clock
                                    ->ofFormat(Format::of('Y-m-d G:i:s'))
                                    ->at(...),
                            ),
                    )
                    ->default('idle_since', Maybe::nothing())
                    ->map(static fn($shape) => Channel::of(
                        $shape['name'],
                        $shape['vhost'],
                        $shape['user'],
                        $shape['number'],
                        $shape['node'],
                        $shape['state'],
                        Channel\Messages::of(
                            $shape['messages_uncommitted'],
                            $shape['messages_unconfirmed'],
                            $shape['messages_unacknowledged'],
                        ),
                        $shape['consumer_count'],
                        $shape['confirm'],
                        $shape['transactional'],
                        $shape['idle_since'],
                    )),
            )
            ->flatMap(static fn($channel) => $channel->maybe()->toSequence());
    }

    /**
     * @return Sequence<Consumer>
     */
    public function consumers(): Sequence
    {
        /** @psalm-suppress MixedArgument,MixedArrayAccess */
        return $this
            ->list('consumers')
            ->map(
                Is::shape(
                    'consumer_tag',
                    Is::string()->map(Tag::of(...)),
                )
                    ->with(
                        'channel_details',
                        Is::shape(
                            'name',
                            Is::string()->map(Channel\Name::of(...)),
                        )
                            ->with(
                                'connection_name',
                                Is::string()->map(Connection\Name::of(...)),
                            ),
                    )
                    ->with(
                        'queue',
                        Is::shape(
                            'name',
                            Is::string(),
                        )
                            ->with(
                                'vhost',
                                Is::string()->map(VHost\Name::of(...)),
                            ),
                    )
                    ->with(
                        'ack_required',
                        Is::bool(),
                    )
                    ->with(
                        'exclusive',
                        Is::bool(),
                    )
                    ->map(static fn($shape) => Consumer::of(
                        $shape['consumer_tag'],
                        $shape['channel_details']['name'],
                        Identity::of(
                            $shape['queue']['name'],
                            $shape['queue']['vhost'],
                        ),
                        $shape['channel_details']['connection_name'],
                        $shape['ack_required'],
                        $shape['exclusive'],
                    )),
            )
            ->flatMap(static fn($consumer) => $consumer->maybe()->toSequence());
    }

    /**
     * @return Sequence<Queue>
     */
    public function queues(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('queues')
            ->map(
                Is::shape(
                    'name',
                    Is::string(),
                )
                    ->with(
                        'vhost',
                        Is::string()->map(VHost\Name::of(...)),
                    )
                    ->with(
                        'messages',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_ready',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'messages_unacknowledged',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->optional(
                        'idle_since',
                        Is::string()
                            ->nonEmpty()
                            ->map(
                                $this
                                    ->clock
                                    ->ofFormat(Format::of('Y-m-d G:i:s'))
                                    ->at(...),
                            ),
                    )
                    ->default('idle_since', Maybe::nothing())
                    ->with(
                        'consumers',
                        Is::int()
                            ->positive()
                            ->or(Is::value(0))
                            ->map(Count::of(...)),
                    )
                    ->with(
                        'state',
                        Is::value('running')
                            ->map(static fn() => State::running)
                            ->or(Is::value('idle')->map(
                                static fn() => State::idle,
                            )),
                    )
                    ->with(
                        'node',
                        Is::string()->map(Node\Name::of(...)),
                    )
                    ->with(
                        'exclusive',
                        Is::bool(),
                    )
                    ->with(
                        'auto_delete',
                        Is::bool(),
                    )
                    ->with(
                        'durable',
                        Is::bool(),
                    )
                    ->map(static fn($shape) => Queue::of(
                        Identity::of(
                            $shape['name'],
                            $shape['vhost'],
                        ),
                        Queue\Messages::of(
                            $shape['messages'],
                            $shape['messages_ready'],
                            $shape['messages_unacknowledged'],
                        ),
                        $shape['idle_since'],
                        $shape['consumers'],
                        $shape['state'],
                        $shape['node'],
                        $shape['exclusive'],
                        $shape['auto_delete'],
                        $shape['durable'],
                    )),
            )
            ->flatMap(static fn($queue) => $queue->maybe()->toSequence());
    }

    /**
     * @return Sequence<Node>
     */
    public function nodes(): Sequence
    {
        /** @psalm-suppress MixedArgument */
        return $this
            ->list('nodes')
            ->map(
                Is::shape(
                    'name',
                    Is::string()
                        ->map(Node\Name::maybe(...))
                        ->and(Is::just()),
                )
                    ->with(
                        'type',
                        Is::value('disc')
                            ->map(static fn() => Node\Type::disc)
                            ->or(Is::value('ram')->map(
                                static fn() => Node\Type::ram,
                            )),
                    )
                    ->with('running', Is::bool())
                    ->map(static fn($shape) => Node::of(
                        $shape['name'],
                        $shape['type'],
                        $shape['running'],
                    )),
            )
            ->flatMap(static fn($node) => $node->maybe()->toSequence());
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
