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
            ->toSetOf(
                User::class,
                static function(array $user): \Generator {
                    /** @var array{name: string, password_hash: string, hashing_algorithm: string, tags: string} $user */
                    $tags = Set::strings();

                    foreach (\explode(',', $user['tags']) as $tag) {
                        $tags = $tags->add($tag);
                    }

                    yield new User(
                        new User\Name($user['name']),
                        new User\Password(
                            $user['password_hash'],
                            $user['hashing_algorithm'],
                        ),
                        $tags,
                    );
                },
            );
    }

    public function vhosts(): Set
    {
        /** @var Set<VHost> */
        return $this
            ->list('vhosts')
            ->toSetOf(
                VHost::class,
                static function(array $vhost): \Generator {
                    /** @var array{name: string, messages: int, messages_ready: int, messages_unacknowledged: int, tracing: bool} $vhost */
                    yield new VHost(
                        new VHost\Name($vhost['name']),
                        new VHost\Messages(
                            new Count($vhost['messages']),
                            new Count($vhost['messages_ready']),
                            new Count($vhost['messages_unacknowledged']),
                        ),
                        $vhost['tracing'],
                    );
                },
            );
    }

    public function connections(): Set
    {
        /** @var Set<Connection> */
        return $this
            ->list('connections')
            ->toSetOf(
                Connection::class,
                function(array $connection): \Generator {
                    /** @var array{name: string, connected_at: int, timeout: int, vhost: string, user: string, protocol: string, auth_mechanism: string, ssl: bool, peer_host: string, peer_port: int, host: string, port: int, node: string, type: 'network'|'direct', state: 'running'|'idle'} $connection */

                    $connectedAt = $connection['connected_at'];

                    /** @psalm-suppress MixedArgument */
                    yield new Connection(
                        new Connection\Name($connection['name']),
                        $this->clock->at(date(
                            \DateTime::ATOM,
                            (int) round($connectedAt / 1000),
                        )),
                        new Timeout($connection['timeout']),
                        new VHost\Name($connection['vhost']),
                        new User\Name($connection['user']),
                        new Protocol($connection['protocol']),
                        AuthenticationMechanism::of($connection['auth_mechanism']),
                        $connection['ssl'],
                        new Peer(
                            Host::of($connection['peer_host']),
                            Port::of($connection['peer_port']),
                        ),
                        Host::of($connection['host']),
                        Port::of($connection['port']),
                        new Node\Name($connection['node']),
                        Connection\Type::{$connection['type']}(),
                        State::{$connection['state']}(),
                    );
                },
            );
    }

    public function exchanges(): Set
    {
        /** @var Set<Exchange> */
        return $this
            ->list('exchanges')
            ->toSetOf(
                Exchange::class,
                static function(array $exchange): \Generator {
                    /** @var array{name: string, vhost: string, type: 'topic'|'headers'|'direct'|'fanout', durable: bool, auto_delete: bool, internal: bool} $exchange */

                    /** @psalm-suppress MixedArgument */
                    yield new Exchange(
                        new Exchange\Name($exchange['name']),
                        new VHost\Name($exchange['vhost']),
                        Exchange\Type::{$exchange['type']}(),
                        $exchange['durable'],
                        $exchange['auto_delete'],
                        $exchange['internal'],
                    );
                },
            );
    }

    public function permissions(): Set
    {
        /** @var Set<Permission> */
        return $this
            ->list('permissions')
            ->toSetOf(
                Permission::class,
                static function(array $permission): \Generator {
                    /** @var array{user: string, vhost: string, configure: string, write: string, read: string} $permission */
                    yield new Permission(
                        new User\Name($permission['user']),
                        new VHost\Name($permission['vhost']),
                        $permission['configure'],
                        $permission['write'],
                        $permission['read'],
                    );
                },
            );
    }

    public function channels(): Set
    {
        /** @var Set<Channel> */
        return $this
            ->list('channels')
            ->toSetOf(
                Channel::class,
                function(array $channel): \Generator {
                    /** @var array{name: string, vhost: string, user: string, number: int, node: string, state: 'running'|'idle', messages_uncommitted: int, messages_unconfirmed: int, messages_unacknowledged: int, consumer_count: 1, confirm: bool, transactional: bool, idle_since: string} $channel */

                    /** @psalm-suppress MixedArgument */
                    yield new Channel(
                        new Channel\Name($channel['name']),
                        new VHost\Name($channel['vhost']),
                        new User\Name($channel['user']),
                        $channel['number'],
                        new Node\Name($channel['node']),
                        State::{$channel['state']}(),
                        new Channel\Messages(
                            new Count($channel['messages_uncommitted']),
                            new Count($channel['messages_unconfirmed']),
                            new Count($channel['messages_unacknowledged']),
                        ),
                        new Count($channel['consumer_count']),
                        $channel['confirm'],
                        $channel['transactional'],
                        $this->clock->at($channel['idle_since']),
                    );
                },
            );
    }

    public function consumers(): Set
    {
        /** @var Set<Consumer> */
        return $this
            ->list('consumers')
            ->toSetOf(
                Consumer::class,
                static function(array $consumer): \Generator {
                    /** @var array{consumer_tag: string, channel_details: array{name: string, connection_name: string}, queue: array{name: string, vhost: string}, ack_required: bool, exclusive: bool} $consumer */
                    yield new Consumer(
                        new Tag($consumer['consumer_tag']),
                        new Channel\Name($consumer['channel_details']['name']),
                        new Identity(
                            $consumer['queue']['name'],
                            new VHost\Name($consumer['queue']['vhost']),
                        ),
                        new Connection\Name($consumer['channel_details']['connection_name']),
                        $consumer['ack_required'],
                        $consumer['exclusive'],
                    );
                },
            );
    }

    public function queues(): Set
    {
        /** @var Set<Queue> */
        return $this
            ->list('queues')
            ->toSetOf(
                Queue::class,
                function(array $queue): \Generator {
                    /** @var array{name: string, vhost: string, messages: int, messages_ready: int, messages_unacknowledged: int, idle_since: string, consumers: int, state: 'running'|'idle', node: string, exclusive: bool, auto_delete: bool, durable: bool} $queue */

                    /** @psalm-suppress MixedArgument */
                    yield new Queue(
                        new Identity(
                            $queue['name'],
                            new VHost\Name($queue['vhost']),
                        ),
                        new Queue\Messages(
                            new Count($queue['messages']),
                            new Count($queue['messages_ready']),
                            new Count($queue['messages_unacknowledged']),
                        ),
                        $this->clock->at($queue['idle_since']),
                        new Count($queue['consumers']),
                        State::{$queue['state']}(),
                        new Node\Name($queue['node']),
                        $queue['exclusive'],
                        $queue['auto_delete'],
                        $queue['durable'],
                    );
                },
            );
    }

    public function nodes(): Set
    {
        /** @var Set<Node> */
        return $this
            ->list('nodes')
            ->toSetOf(
                Node::class,
                static function(array $node): \Generator {
                    /** @var array{name: string, type: 'disc'|'ram', running: bool} $node */

                    /** @psalm-suppress MixedArgument */
                    yield new Node(
                        new Node\Name($node['name']),
                        Node\Type::{$node['type']}(),
                        $node['running'],
                    );
                },
            );
    }

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
        $process->wait();

        if (!$process->exitCode()->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        /** @var array */
        $elements = \json_decode($process->output()->toString(), true);

        return Sequence::mixed(...$elements);
    }
}
