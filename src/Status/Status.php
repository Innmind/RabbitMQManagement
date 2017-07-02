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
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Url\Authority\{
    Host,
    Port
};
use Innmind\Immutable\{
    SetInterface,
    Set,
    Sequence
};

final class Status implements StatusInterface
{
    private $server;
    private $clock;
    private $environment;
    private $command;

    public function __construct(
        Server $server,
        TimeContinuumInterface $clock,
        Environment $environment = null
    ) {
        $this->server = $server;
        $this->clock = $clock;
        $this->environment = $environment ?? new Environment\Local;
        $this->command = (new Command('rabbitmqadmin'))
            ->withShortOption('f', 'raw_json')
            ->withArgument('list');
    }

    /**
     * {@inheritdoc}
     */
    public function users(): SetInterface
    {
        return $this
            ->list('users')
            ->reduce(
                new Set(User::class),
                static function(Set $users, array $user): Set {
                    $tags = new Set('string');

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

    /**
     * {@inheritdoc}
     */
    public function vhosts(): SetInterface
    {
        return $this
            ->list('vhosts')
            ->reduce(
                new Set(VHost::class),
                static function(Set $vhosts, array $vhost): Set {
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

    /**
     * {@inheritdoc}
     */
    public function connections(): SetInterface
    {
        return $this
            ->list('connections')
            ->reduce(
                new Set(Connection::class),
                function(Set $connections, array $connection): Set {
                    return $connections->add(new Connection(
                        new ConnectionName($connection['name']),
                        $this->clock->at(date(
                            \DateTime::ATOM,
                            (int) round($connection['connected_at'] / 1000)
                        )),
                        new Timeout($connection['timeout']),
                        new VHostName($connection['vhost']),
                        new UserName($connection['user']),
                        new Protocol($connection['protocol']),
                        AuthenticationMechanism::fromString($connection['auth_mechanism']),
                        $connection['ssl'],
                        new Peer(
                            new Host($connection['peer_host']),
                            new Port($connection['peer_port'])
                        ),
                        new Host($connection['host']),
                        new Port($connection['port']),
                        new NodeName($connection['node']),
                        ConnectionType::{$connection['type']}(),
                        State::{$connection['state']}()
                    ));
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function exchanges(): SetInterface
    {
        return $this
            ->list('exchanges')
            ->reduce(
                new Set(Exchange::class),
                static function(Set $exchanges, array $exchange): Set {
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

    /**
     * {@inheritdoc}
     */
    public function permissions(): SetInterface
    {
        return $this
            ->list('permissions')
            ->reduce(
                new Set(Permission::class),
                static function(Set $permissions, array $permission): Set {
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

    /**
     * {@inheritdoc}
     */
    public function channels(): SetInterface
    {
        return $this
            ->list('channels')
            ->reduce(
                new Set(Channel::class),
                function(Set $channels, array $channel): Set {
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

    /**
     * {@inheritdoc}
     */
    public function consumers(): SetInterface
    {
        return $this
            ->list('consumers')
            ->reduce(
                new Set(Consumer::class),
                static function(Set $consumers, array $consumer): Set {
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

    /**
     * {@inheritdoc}
     */
    public function queues(): SetInterface
    {
        return $this
            ->list('queues')
            ->reduce(
                new Set(Queue::class),
                function(Set $queues, array $queue): Set {
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

    /**
     * {@inheritdoc}
     */
    public function nodes(): SetInterface
    {
        return $this
            ->list('nodes')
            ->reduce(
                new Set(Node::class),
                static function(Set $nodes, array $node): Set {
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
            )
            ->wait();

        if (!$process->exitCode()->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return new Sequence(
            ...json_decode((string) $process->output(), true)
        );
    }
}
