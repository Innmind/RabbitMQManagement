<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status;

use Innmind\RabbitMQ\Management\{
    Status as StatusInterface,
    Model\User,
    Model\User\Name as UserName,
    Model\User\Password,
    Model\VHost,
    Model\VHost\Name as VHostName,
    Model\VHost\Messages as VHostMessages,
    Model\Count,
    Model\Connection,
    Model\Connection\Name as ConnectionName,
    Model\Connection\Timeout,
    Model\Connection\Protocol,
    Model\Connection\AuthenticationMechanism,
    Model\Connection\Peer,
    Model\Connection\Type as ConnectionType,
    Model\State,
    Model\Node,
    Model\Node\Name as NodeName,
    Model\Node\Type as NodeType,
    Model\Exchange,
    Model\Exchange\Name as ExchangeName,
    Model\Exchange\Type as ExchangeType,
    Model\Permission,
    Model\Channel,
    Model\Channel\Name as ChannelName,
    Model\Channel\Messages as ChannelMessages,
    Model\Consumer,
    Model\Consumer\Tag,
    Model\Queue,
    Model\Queue\Identity,
    Model\Queue\Messages as QueueMessages,
    Exception\ManagementPluginFailedToRun
};
use Innmind\Server\Control\{
    Server,
    Server\Command
};
use Innmind\TimeContinuum\Clock;
use Innmind\Url\Authority\{
    Host,
    Port
};
use Innmind\Immutable\{
    Set,
    Sequence,
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
        Environment $environment = null
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
        /** @var Set<User> */
        return $this
            ->list('users')
            ->reduce(
                Set::of(User::class),
                static function(Set $users, array $user): Set {
                    /** @var array{name: string, password_hash: string, hashing_algorithm: string, tags: string} $user */
                    $tags = Set::strings();

                    foreach (explode(',', $user['tags']) as $tag) {
                        $tags = $tags->add($tag);
                    }

                    return $users->add(new User(
                        new UserName($user['name']),
                        new Password(
                            $user['password_hash'],
                            $user['hashing_algorithm']
                        ),
                        $tags
                    ));
                }
            );
    }

    public function vhosts(): Set
    {
        /** @var Set<VHost> */
        return $this
            ->list('vhosts')
            ->reduce(
                Set::of(VHost::class),
                static function(Set $vhosts, array $vhost): Set {
                    /** @var array{name: string, messages: int, messages_ready: int, messages_unacknowledged: int, tracing: bool} $vhost */
                    return $vhosts->add(new VHost(
                        new VHostName($vhost['name']),
                        new VHostMessages(
                            new Count($vhost['messages']),
                            new Count($vhost['messages_ready']),
                            new Count($vhost['messages_unacknowledged'])
                        ),
                        $vhost['tracing']
                    ));
                }
            );
    }

    public function connections(): Set
    {
        /** @var Set<Connection> */
        return $this
            ->list('connections')
            ->reduce(
                Set::of(Connection::class),
                function(Set $connections, array $connection): Set {
                    /** @var array{name: string, connected_at: int, timeout: int, vhost: string, user: string, protocol: string, auth_mechanism: string, ssl: bool, peer_host: string, peer_port: int, host: string, port: int, node: string, type: 'network'|'direct', state: 'running'|'idle'} $connection */

                    $connectedAt = $connection['connected_at'];

                    /** @psalm-suppress MixedArgument */
                    return $connections->add(new Connection(
                        new ConnectionName($connection['name']),
                        $this->clock->at(date(
                            \DateTime::ATOM,
                            (int) round($connectedAt / 1000)
                        )),
                        new Timeout($connection['timeout']),
                        new VHostName($connection['vhost']),
                        new UserName($connection['user']),
                        new Protocol($connection['protocol']),
                        AuthenticationMechanism::of($connection['auth_mechanism']),
                        $connection['ssl'],
                        new Peer(
                            Host::of($connection['peer_host']),
                            Port::of($connection['peer_port'])
                        ),
                        Host::of($connection['host']),
                        Port::of($connection['port']),
                        new NodeName($connection['node']),
                        ConnectionType::{$connection['type']}(),
                        State::{$connection['state']}()
                    ));
                }
            );
    }

    public function exchanges(): Set
    {
        /** @var Set<Exchange> */
        return $this
            ->list('exchanges')
            ->reduce(
                Set::of(Exchange::class),
                static function(Set $exchanges, array $exchange): Set {
                    /** @var array{name: string, vhost: string, type: 'topic'|'headers'|'direct'|'fanout', durable: bool, auto_delete: bool, internal: bool} $exchange */

                    /** @psalm-suppress MixedArgument */
                    return $exchanges->add(new Exchange(
                        new ExchangeName($exchange['name']),
                        new VHostName($exchange['vhost']),
                        ExchangeType::{$exchange['type']}(),
                        $exchange['durable'],
                        $exchange['auto_delete'],
                        $exchange['internal']
                    ));
                }
            );
    }

    public function permissions(): Set
    {
        /** @var Set<Permission> */
        return $this
            ->list('permissions')
            ->reduce(
                Set::of(Permission::class),
                static function(Set $permissions, array $permission): Set {
                    /** @var array{user: string, vhost: string, configure: string, write: string, read: string} $permission */
                    return $permissions->add(new Permission(
                        new UserName($permission['user']),
                        new VHostName($permission['vhost']),
                        $permission['configure'],
                        $permission['write'],
                        $permission['read']
                    ));
                }
            );
    }

    public function channels(): Set
    {
        /** @var Set<Channel> */
        return $this
            ->list('channels')
            ->reduce(
                Set::of(Channel::class),
                function(Set $channels, array $channel): Set {
                    /** @var array{name: string, vhost: string, user: string, number: int, node: string, state: 'running'|'idle', messages_uncommitted: int, messages_unconfirmed: int, messages_unacknowledged: int, consumer_count: 1, confirm: bool, transactional: bool, idle_since: string} $channel */

                    /** @psalm-suppress MixedArgument */
                    return $channels->add(new Channel(
                        new ChannelName($channel['name']),
                        new VHostName($channel['vhost']),
                        new UserName($channel['user']),
                        $channel['number'],
                        new NodeName($channel['node']),
                        State::{$channel['state']}(),
                        new ChannelMessages(
                            new Count($channel['messages_uncommitted']),
                            new Count($channel['messages_unconfirmed']),
                            new Count($channel['messages_unacknowledged'])
                        ),
                        new Count($channel['consumer_count']),
                        $channel['confirm'],
                        $channel['transactional'],
                        $this->clock->at($channel['idle_since'])
                    ));
                }
            );
    }

    public function consumers(): Set
    {
        /** @var Set<Consumer> */
        return $this
            ->list('consumers')
            ->reduce(
                Set::of(Consumer::class),
                static function(Set $consumers, array $consumer): Set {
                    /** @var array{consumer_tag: string, channel_details: array{name: string, connection_name: string}, queue: array{name: string, vhost: string}, ack_required: bool, exclusive: bool} $consumer */
                    return $consumers->add(new Consumer(
                        new Tag($consumer['consumer_tag']),
                        new ChannelName($consumer['channel_details']['name']),
                        new Identity(
                            $consumer['queue']['name'],
                            new VHostName($consumer['queue']['vhost'])
                        ),
                        new ConnectionName($consumer['channel_details']['connection_name']),
                        $consumer['ack_required'],
                        $consumer['exclusive']
                    ));
                }
            );
    }

    public function queues(): Set
    {
        /** @var Set<Queue> */
        return $this
            ->list('queues')
            ->reduce(
                Set::of(Queue::class),
                function(Set $queues, array $queue): Set {
                    /** @var array{name: string, vhost: string, messages: int, messages_ready: int, messages_unacknowledged: int, idle_since: string, consumers: int, state: 'running'|'idle', node: string, exclusive: bool, auto_delete: bool, durable: bool} $queue */

                    /** @psalm-suppress MixedArgument */
                    return $queues->add(new Queue(
                        new Identity(
                            $queue['name'],
                            new VHostName($queue['vhost'])
                        ),
                        new QueueMessages(
                            new Count($queue['messages']),
                            new Count($queue['messages_ready']),
                            new Count($queue['messages_unacknowledged'])
                        ),
                        $this->clock->at($queue['idle_since']),
                        new Count($queue['consumers']),
                        State::{$queue['state']}(),
                        new NodeName($queue['node']),
                        $queue['exclusive'],
                        $queue['auto_delete'],
                        $queue['durable']
                    ));
                }
            );
    }

    public function nodes(): Set
    {
        /** @var Set<Node> */
        return $this
            ->list('nodes')
            ->reduce(
                Set::of(Node::class),
                static function(Set $nodes, array $node): Set {
                    /** @var array{name: string, type: 'disc'|'ram', running: bool} $node */

                    /** @psalm-suppress MixedArgument */
                    return $nodes->add(new Node(
                        new NodeName($node['name']),
                        NodeType::{$node['type']}(),
                        $node['running']
                    ));
                }
            );
    }

    private function list(string $element): Sequence
    {
        $process = $this
            ->server
            ->processes()
            ->execute(
                ($this->environment)(
                    $this->command->withArgument($element)
                )
            );
        $process->wait();

        if (!$process->exitCode()->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        /** @var array */
        $elements = json_decode($process->output()->toString(), true);

        return Sequence::mixed(...$elements);
    }
}
