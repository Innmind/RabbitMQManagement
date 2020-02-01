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

    /**
     * {@inheritdoc}
     */
    public function users(): Set
    {
        return $this
            ->list('users')
            ->reduce(
                Set::of(User::class),
                static function(Set $users, array $user): Set {
                    $tags = Set::of('string');

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
    public function vhosts(): Set
    {
        return $this
            ->list('vhosts')
            ->reduce(
                Set::of(VHost::class),
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
    public function connections(): Set
    {
        return $this
            ->list('connections')
            ->reduce(
                Set::of(Connection::class),
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

    /**
     * {@inheritdoc}
     */
    public function exchanges(): Set
    {
        return $this
            ->list('exchanges')
            ->reduce(
                Set::of(Exchange::class),
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
    public function permissions(): Set
    {
        return $this
            ->list('permissions')
            ->reduce(
                Set::of(Permission::class),
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
    public function channels(): Set
    {
        return $this
            ->list('channels')
            ->reduce(
                Set::of(Channel::class),
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
    public function consumers(): Set
    {
        return $this
            ->list('consumers')
            ->reduce(
                Set::of(Consumer::class),
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
    public function queues(): Set
    {
        return $this
            ->list('queues')
            ->reduce(
                Set::of(Queue::class),
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
    public function nodes(): Set
    {
        return $this
            ->list('nodes')
            ->reduce(
                Set::of(Node::class),
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
            );
        $process->wait();

        if (!$process->exitCode()->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return Sequence::mixed(
            ...json_decode($process->output()->toString(), true),
        );
    }
}
