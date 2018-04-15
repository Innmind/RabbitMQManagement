<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Status;

use Innmind\RabbitMQ\Management\{
    Status\Status,
    Status as StatusInterface,
    Model\User,
    Model\VHost,
    Model\Connection,
    Model\Exchange,
    Model\Permission,
    Model\Channel,
    Model\Consumer,
    Model\Queue,
    Model\Node,
    Status\Environment\Remote
};
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Command,
    Server\Process,
    Server\Process\Output,
    Server\Process\ExitCode
};
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    PointInTimeInterface
};
use Innmind\Url\Authority\Host;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            StatusInterface::class,
            new Status(
                $this->createMock(Server::class),
                $this->createMock(TimeContinuumInterface::class)
            )
        );
    }

    public function testUsers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'users'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"name":"guest","password_hash":"ZbsaALrYfNHzlDnxzIZVSzP87B/sYM/lM+kZELz3qRk7vod+","hashing_algorithm":"rabbit_password_hashing_sha256","tags":"administrator"}]');

        $users = $status->users();

        $this->assertInstanceOf(SetInterface::class, $users);
        $this->assertSame(User::class, (string) $users->type());
        $this->assertCount(1, $users);
        $this->assertSame('guest', (string) $users->current()->name());
        $this->assertSame(
            'ZbsaALrYfNHzlDnxzIZVSzP87B/sYM/lM+kZELz3qRk7vod+',
            $users->current()->password()->hash()
        );
        $this->assertSame(
            'rabbit_password_hashing_sha256',
            $users->current()->password()->algorithm()
        );
        $this->assertCount(1, $users->current()->tags());
        $this->assertSame(['administrator'], $users->current()->tags()->toPrimitive());
    }

    public function testRemoteUsers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"name":"guest","password_hash":"ZbsaALrYfNHzlDnxzIZVSzP87B/sYM/lM+kZELz3qRk7vod+","hashing_algorithm":"rabbit_password_hashing_sha256","tags":"administrator"}]');

        $users = $status->users();

        $this->assertInstanceOf(SetInterface::class, $users);
        $this->assertSame(User::class, (string) $users->type());
        $this->assertCount(1, $users);
        $this->assertSame('guest', (string) $users->current()->name());
        $this->assertSame(
            'ZbsaALrYfNHzlDnxzIZVSzP87B/sYM/lM+kZELz3qRk7vod+',
            $users->current()->password()->hash()
        );
        $this->assertSame(
            'rabbit_password_hashing_sha256',
            $users->current()->password()->algorithm()
        );
        $this->assertCount(1, $users->current()->tags());
        $this->assertSame(['administrator'], $users->current()->tags()->toPrimitive());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListUsers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'users'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->users();
    }

    public function testVhosts()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'vhosts'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"messages":1,"messages_details":{"rate":0.0},"messages_ready":2,"messages_ready_details":{"rate":0.0},"messages_unacknowledged":3,"messages_unacknowledged_details":{"rate":0.0},"name":"/","tracing":false}]');

        $vhosts = $status->vhosts();

        $this->assertInstanceOf(SetInterface::class, $vhosts);
        $this->assertSame(VHost::class, (string) $vhosts->type());
        $this->assertCount(1, $vhosts);
        $this->assertSame('/', (string) $vhosts->current()->name());
        $this->assertFalse($vhosts->current()->tracing());
        $this->assertSame(1, $vhosts->current()->messages()->total()->toInt());
        $this->assertSame(2, $vhosts->current()->messages()->ready()->toInt());
        $this->assertSame(3, $vhosts->current()->messages()->unacknowledged()->toInt());
    }

    public function testRemoteVhosts()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'vhosts' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"messages":1,"messages_details":{"rate":0.0},"messages_ready":2,"messages_ready_details":{"rate":0.0},"messages_unacknowledged":3,"messages_unacknowledged_details":{"rate":0.0},"name":"/","tracing":false}]');

        $vhosts = $status->vhosts();

        $this->assertInstanceOf(SetInterface::class, $vhosts);
        $this->assertSame(VHost::class, (string) $vhosts->type());
        $this->assertCount(1, $vhosts);
        $this->assertSame('/', (string) $vhosts->current()->name());
        $this->assertFalse($vhosts->current()->tracing());
        $this->assertSame(1, $vhosts->current()->messages()->total()->toInt());
        $this->assertSame(2, $vhosts->current()->messages()->ready()->toInt());
        $this->assertSame(3, $vhosts->current()->messages()->unacknowledged()->toInt());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListVhosts()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'vhosts'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->vhosts();
    }

    public function testConnections()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'connections'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"connected_at":1498810410111,"client_properties":{"product":"AMQPLib","platform":"PHP","version":"2.6","information":"","copyright":"","capabilities":{"authentication_failure_close":true,"publisher_confirms":true,"consumer_cancel_notify":true,"exchange_exchange_bindings":true,"basic.nack":true,"connection.blocked":true}},"channel_max":65535,"frame_max":131072,"timeout":60,"vhost":"/","user":"guest","protocol":"AMQP 0-9-1","ssl_hash":null,"ssl_cipher":null,"ssl_key_exchange":null,"ssl_protocol":null,"auth_mechanism":"AMQPLAIN","peer_cert_validity":null,"peer_cert_issuer":null,"peer_cert_subject":null,"ssl":false,"peer_host":"172.19.0.1","host":"172.19.0.2","peer_port":32788,"port":5672,"name":"172.19.0.1:32788 -> 172.19.0.2:5672","node":"rabbit@050becbb9cb3","type":"network","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":5},"reductions":3607,"channels":0,"state":"running","send_pend":0,"send_cnt":3,"recv_cnt":3,"recv_oct_details":{"rate":0.0},"recv_oct":357,"send_oct_details":{"rate":0.0},"send_oct":526,"reductions_details":{"rate":0.0},"reductions":3607}]');
        $clock
            ->expects($this->once())
            ->method('at')
            ->with('2017-06-30T08:13:30+00:00')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));

        $connections = $status->connections();

        $this->assertInstanceOf(SetInterface::class, $connections);
        $this->assertSame(Connection::class, (string) $connections->type());
        $this->assertCount(1, $connections);
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672',
            (string) $connections->current()->name()
        );
        $this->assertSame($date, $connections->current()->connectedAt());
        $this->assertSame(60, $connections->current()->timeout()->toInt());
        $this->assertSame('/', (string) $connections->current()->vhost());
        $this->assertSame('guest', (string) $connections->current()->user());
        $this->assertSame('AMQP 0-9-1', (string) $connections->current()->protocol());
        $this->assertSame('AMQPLAIN', (string) $connections->current()->authenticationMechanism());
        $this->assertFalse($connections->current()->ssl());
        $this->assertSame('172.19.0.1', (string) $connections->current()->peer()->host());
        $this->assertSame(32788, $connections->current()->peer()->port()->value());
        $this->assertSame('172.19.0.2', (string) $connections->current()->host());
        $this->assertSame(5672, $connections->current()->port()->value());
        $this->assertSame('rabbit@050becbb9cb3', (string) $connections->current()->node());
        $this->assertSame('network', (string) $connections->current()->type());
        $this->assertSame('running', (string) $connections->current()->state());
    }

    public function testRemoteConnections()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'connections' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"connected_at":1498810410111,"client_properties":{"product":"AMQPLib","platform":"PHP","version":"2.6","information":"","copyright":"","capabilities":{"authentication_failure_close":true,"publisher_confirms":true,"consumer_cancel_notify":true,"exchange_exchange_bindings":true,"basic.nack":true,"connection.blocked":true}},"channel_max":65535,"frame_max":131072,"timeout":60,"vhost":"/","user":"guest","protocol":"AMQP 0-9-1","ssl_hash":null,"ssl_cipher":null,"ssl_key_exchange":null,"ssl_protocol":null,"auth_mechanism":"AMQPLAIN","peer_cert_validity":null,"peer_cert_issuer":null,"peer_cert_subject":null,"ssl":false,"peer_host":"172.19.0.1","host":"172.19.0.2","peer_port":32788,"port":5672,"name":"172.19.0.1:32788 -> 172.19.0.2:5672","node":"rabbit@050becbb9cb3","type":"network","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":5},"reductions":3607,"channels":0,"state":"running","send_pend":0,"send_cnt":3,"recv_cnt":3,"recv_oct_details":{"rate":0.0},"recv_oct":357,"send_oct_details":{"rate":0.0},"send_oct":526,"reductions_details":{"rate":0.0},"reductions":3607}]');
        $clock
            ->expects($this->once())
            ->method('at')
            ->with('2017-06-30T08:13:30+00:00')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));

        $connections = $status->connections();

        $this->assertInstanceOf(SetInterface::class, $connections);
        $this->assertSame(Connection::class, (string) $connections->type());
        $this->assertCount(1, $connections);
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672',
            (string) $connections->current()->name()
        );
        $this->assertSame($date, $connections->current()->connectedAt());
        $this->assertSame(60, $connections->current()->timeout()->toInt());
        $this->assertSame('/', (string) $connections->current()->vhost());
        $this->assertSame('guest', (string) $connections->current()->user());
        $this->assertSame('AMQP 0-9-1', (string) $connections->current()->protocol());
        $this->assertSame('AMQPLAIN', (string) $connections->current()->authenticationMechanism());
        $this->assertFalse($connections->current()->ssl());
        $this->assertSame('172.19.0.1', (string) $connections->current()->peer()->host());
        $this->assertSame(32788, $connections->current()->peer()->port()->value());
        $this->assertSame('172.19.0.2', (string) $connections->current()->host());
        $this->assertSame(5672, $connections->current()->port()->value());
        $this->assertSame('rabbit@050becbb9cb3', (string) $connections->current()->node());
        $this->assertSame('network', (string) $connections->current()->type());
        $this->assertSame('running', (string) $connections->current()->state());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListConnections()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'connections'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->connections();
    }

    public function testExchanges()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'exchanges'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"name":"","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.direct","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.fanout","vhost":"/","type":"fanout","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.headers","vhost":"/","type":"headers","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.match","vhost":"/","type":"headers","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.rabbitmq.log","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":true,"arguments":{}},{"name":"amq.rabbitmq.trace","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":true,"arguments":{}},{"name":"amq.topic","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"crawl","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}}]');

        $exchanges = $status->exchanges();

        $this->assertInstanceOf(SetInterface::class, $exchanges);
        $this->assertSame(Exchange::class, (string) $exchanges->type());
        $this->assertCount(9, $exchanges);
        $this->assertSame('', (string) $exchanges->current()->name());
        $this->assertSame('/', (string) $exchanges->current()->vhost());
        $this->assertSame('direct', (string) $exchanges->current()->type());
        $this->assertTrue($exchanges->current()->durable());
        $this->assertFalse($exchanges->current()->autoDelete());
        $this->assertFalse($exchanges->current()->internal());
    }

    public function testRemoteExchanges()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'exchanges' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"name":"","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.direct","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.fanout","vhost":"/","type":"fanout","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.headers","vhost":"/","type":"headers","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.match","vhost":"/","type":"headers","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"amq.rabbitmq.log","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":true,"arguments":{}},{"name":"amq.rabbitmq.trace","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":true,"arguments":{}},{"name":"amq.topic","vhost":"/","type":"topic","durable":true,"auto_delete":false,"internal":false,"arguments":{}},{"name":"crawl","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}}]');

        $exchanges = $status->exchanges();

        $this->assertInstanceOf(SetInterface::class, $exchanges);
        $this->assertSame(Exchange::class, (string) $exchanges->type());
        $this->assertCount(9, $exchanges);
        $this->assertSame('', (string) $exchanges->current()->name());
        $this->assertSame('/', (string) $exchanges->current()->vhost());
        $this->assertSame('direct', (string) $exchanges->current()->type());
        $this->assertTrue($exchanges->current()->durable());
        $this->assertFalse($exchanges->current()->autoDelete());
        $this->assertFalse($exchanges->current()->internal());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListExchanges()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'exchanges'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->exchanges();
    }

    public function testPermissions()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'permissions'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"user":"guest","vhost":"/","configure":".*","write":".*","read":".*"}]');

        $permissions = $status->permissions();

        $this->assertInstanceOf(SetInterface::class, $permissions);
        $this->assertSame(Permission::class, (string) $permissions->type());
        $this->assertCount(1, $permissions);
        $this->assertSame('guest', (string) $permissions->current()->user());
        $this->assertSame('/', (string) $permissions->current()->vhost());
        $this->assertSame('.*', (string) $permissions->current()->configure());
        $this->assertSame('.*', (string) $permissions->current()->write());
        $this->assertSame('.*', (string) $permissions->current()->read());
    }

    public function testRemotePermissions()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'permissions' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"user":"guest","vhost":"/","configure":".*","write":".*","read":".*"}]');

        $permissions = $status->permissions();

        $this->assertInstanceOf(SetInterface::class, $permissions);
        $this->assertSame(Permission::class, (string) $permissions->type());
        $this->assertCount(1, $permissions);
        $this->assertSame('guest', (string) $permissions->current()->user());
        $this->assertSame('/', (string) $permissions->current()->vhost());
        $this->assertSame('.*', (string) $permissions->current()->configure());
        $this->assertSame('.*', (string) $permissions->current()->write());
        $this->assertSame('.*', (string) $permissions->current()->read());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListPermissions()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'permissions'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->permissions();
    }

    public function testChannels()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'channels'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"vhost":"/","user":"guest","number":1,"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (1)","node":"rabbit@050becbb9cb3","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":3},"reductions":2401,"state":"running","global_prefetch_count":0,"prefetch_count":0,"acks_uncommitted":0,"messages_uncommitted":2,"messages_unconfirmed":3,"messages_unacknowledged":4,"consumer_count":1,"confirm":false,"transactional":false,"idle_since":"2017-06-30 8:13:31","reductions_details":{"rate":0.0},"reductions":2401,"connection_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}},{"vhost":"/","user":"guest","number":2,"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (2)","node":"rabbit@050becbb9cb3","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":6},"reductions":905,"state":"running","global_prefetch_count":0,"prefetch_count":0,"acks_uncommitted":0,"messages_uncommitted":0,"messages_unconfirmed":0,"messages_unacknowledged":0,"consumer_count":0,"confirm":false,"transactional":false,"idle_since":"2017-06-30 8:13:31","reductions_details":{"rate":0.0},"reductions":905,"connection_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}}]');
        $clock
            ->expects($this->at(0))
            ->method('at')
            ->with('2017-06-30 8:13:31')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));
        $clock
            ->expects($this->at(1))
            ->method('at')
            ->with('2017-06-30 8:13:31');

        $channels = $status->channels();

        $this->assertInstanceOf(SetInterface::class, $channels);
        $this->assertSame(Channel::class, (string) $channels->type());
        $this->assertCount(2, $channels);
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672 (1)',
            (string) $channels->current()->name()
        );
        $this->assertSame('/', (string) $channels->current()->vhost());
        $this->assertSame('guest', (string) $channels->current()->user());
        $this->assertSame(1, $channels->current()->number());
        $this->assertSame('rabbit@050becbb9cb3', (string) $channels->current()->node());
        $this->assertSame('running', (string) $channels->current()->state());
        $this->assertSame(2, $channels->current()->messages()->uncommitted()->toInt());
        $this->assertSame(3, $channels->current()->messages()->unconfirmed()->toInt());
        $this->assertSame(4, $channels->current()->messages()->unacknowledged()->toInt());
        $this->assertSame(1, $channels->current()->consumers()->toInt());
        $this->assertFalse($channels->current()->confirm());
        $this->assertFalse($channels->current()->transactional());
        $this->assertSame($date, $channels->current()->idleSince());
    }

    public function testRemoteChannels()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'channels' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"vhost":"/","user":"guest","number":1,"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (1)","node":"rabbit@050becbb9cb3","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":3},"reductions":2401,"state":"running","global_prefetch_count":0,"prefetch_count":0,"acks_uncommitted":0,"messages_uncommitted":2,"messages_unconfirmed":3,"messages_unacknowledged":4,"consumer_count":1,"confirm":false,"transactional":false,"idle_since":"2017-06-30 8:13:31","reductions_details":{"rate":0.0},"reductions":2401,"connection_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}},{"vhost":"/","user":"guest","number":2,"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (2)","node":"rabbit@050becbb9cb3","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":6},"reductions":905,"state":"running","global_prefetch_count":0,"prefetch_count":0,"acks_uncommitted":0,"messages_uncommitted":0,"messages_unconfirmed":0,"messages_unacknowledged":0,"consumer_count":0,"confirm":false,"transactional":false,"idle_since":"2017-06-30 8:13:31","reductions_details":{"rate":0.0},"reductions":905,"connection_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}}]');
        $clock
            ->expects($this->at(0))
            ->method('at')
            ->with('2017-06-30 8:13:31')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));
        $clock
            ->expects($this->at(1))
            ->method('at')
            ->with('2017-06-30 8:13:31');

        $channels = $status->channels();

        $this->assertInstanceOf(SetInterface::class, $channels);
        $this->assertSame(Channel::class, (string) $channels->type());
        $this->assertCount(2, $channels);
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672 (1)',
            (string) $channels->current()->name()
        );
        $this->assertSame('/', (string) $channels->current()->vhost());
        $this->assertSame('guest', (string) $channels->current()->user());
        $this->assertSame(1, $channels->current()->number());
        $this->assertSame('rabbit@050becbb9cb3', (string) $channels->current()->node());
        $this->assertSame('running', (string) $channels->current()->state());
        $this->assertSame(2, $channels->current()->messages()->uncommitted()->toInt());
        $this->assertSame(3, $channels->current()->messages()->unconfirmed()->toInt());
        $this->assertSame(4, $channels->current()->messages()->unacknowledged()->toInt());
        $this->assertSame(1, $channels->current()->consumers()->toInt());
        $this->assertFalse($channels->current()->confirm());
        $this->assertFalse($channels->current()->transactional());
        $this->assertSame($date, $channels->current()->idleSince());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListChannels()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'channels'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->channels();
    }

    public function testConsumers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'consumers'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"arguments":{},"prefetch_count":0,"ack_required":true,"exclusive":false,"consumer_tag":"PHPPROCESS_Baptouuuu.local_7267","queue":{"name":"crawl","vhost":"/"},"channel_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (1)","number":1,"user":"guest","connection_name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}}]');

        $consumers = $status->consumers();

        $this->assertInstanceOf(SetInterface::class, $consumers);
        $this->assertSame(Consumer::class, (string) $consumers->type());
        $this->assertCount(1, $consumers);
        $this->assertSame(
            'PHPPROCESS_Baptouuuu.local_7267',
            (string) $consumers->current()->tag()
        );
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672 (1)',
            (string) $consumers->current()->channel()
        );
        $this->assertSame(
            'crawl',
            (string) $consumers->current()->queue()->name()
        );
        $this->assertSame(
            '/',
            (string) $consumers->current()->queue()->vhost()
        );
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672',
            (string) $consumers->current()->connection()
        );
        $this->assertTrue($consumers->current()->ackRequired());
        $this->assertFalse($consumers->current()->exclusive());
    }

    public function testRemoteConsumers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'consumers' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"arguments":{},"prefetch_count":0,"ack_required":true,"exclusive":false,"consumer_tag":"PHPPROCESS_Baptouuuu.local_7267","queue":{"name":"crawl","vhost":"/"},"channel_details":{"name":"172.19.0.1:32788 -> 172.19.0.2:5672 (1)","number":1,"user":"guest","connection_name":"172.19.0.1:32788 -> 172.19.0.2:5672","peer_port":32788,"peer_host":"172.19.0.1"}}]');

        $consumers = $status->consumers();

        $this->assertInstanceOf(SetInterface::class, $consumers);
        $this->assertSame(Consumer::class, (string) $consumers->type());
        $this->assertCount(1, $consumers);
        $this->assertSame(
            'PHPPROCESS_Baptouuuu.local_7267',
            (string) $consumers->current()->tag()
        );
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672 (1)',
            (string) $consumers->current()->channel()
        );
        $this->assertSame(
            'crawl',
            (string) $consumers->current()->queue()->name()
        );
        $this->assertSame(
            '/',
            (string) $consumers->current()->queue()->vhost()
        );
        $this->assertSame(
            '172.19.0.1:32788 -> 172.19.0.2:5672',
            (string) $consumers->current()->connection()
        );
        $this->assertTrue($consumers->current()->ackRequired());
        $this->assertFalse($consumers->current()->exclusive());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListConsumers()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'consumers'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->consumers();
    }

    public function testQueues()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'queues'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"memory":14184,"reductions":8715,"reductions_details":{"rate":0.0},"messages":1,"messages_details":{"rate":0.0},"messages_ready":2,"messages_ready_details":{"rate":0.0},"messages_unacknowledged":3,"messages_unacknowledged_details":{"rate":0.0},"idle_since":"2017-06-30 8:13:31","consumer_utilisation":null,"policy":null,"exclusive_consumer_tag":null,"consumers":1,"recoverable_slaves":null,"state":"running","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":1},"messages_ram":0,"messages_ready_ram":0,"messages_unacknowledged_ram":0,"messages_persistent":0,"message_bytes":0,"message_bytes_ready":0,"message_bytes_unacknowledged":0,"message_bytes_ram":0,"message_bytes_persistent":0,"head_message_timestamp":null,"disk_reads":0,"disk_writes":0,"backing_queue_status":{"mode":"default","q1":0,"q2":0,"delta":["delta","undefined",0,"undefined"],"q3":0,"q4":0,"len":0,"target_ram_count":"infinity","next_seq_id":0,"avg_ingress_rate":0.0,"avg_egress_rate":0.0,"avg_ack_ingress_rate":0.0,"avg_ack_egress_rate":0.0},"node":"rabbit@050becbb9cb3","arguments":{},"exclusive":false,"auto_delete":false,"durable":true,"vhost":"/","name":"crawl"}]');
        $clock
            ->expects($this->once())
            ->method('at')
            ->with('2017-06-30 8:13:31')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));

        $queues = $status->queues();

        $this->assertInstanceOf(SetInterface::class, $queues);
        $this->assertSame(Queue::class, (string) $queues->type());
        $this->assertCount(1, $queues);
        $this->assertSame(
            'crawl',
            (string) $queues->current()->identity()->name()
        );
        $this->assertSame(
            '/',
            (string) $queues->current()->identity()->vhost()
        );
        $this->assertSame(
            1,
            $queues->current()->messages()->total()->toInt()
        );
        $this->assertSame(
            2,
            $queues->current()->messages()->ready()->toInt()
        );
        $this->assertSame(
            3,
            $queues->current()->messages()->unacknowledged()->toInt()
        );
        $this->assertSame(
            $date,
            $queues->current()->idleSince()
        );
        $this->assertSame(1, $queues->current()->consumers()->toInt());
        $this->assertSame('running', (string) $queues->current()->state());
        $this->assertSame('rabbit@050becbb9cb3', (string) $queues->current()->node());
        $this->assertFalse($queues->current()->exclusive());
        $this->assertFalse($queues->current()->autoDelete());
        $this->assertTrue($queues->current()->durable());
    }

    public function testRemoteQueues()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $clock = $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'queues' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"memory":14184,"reductions":8715,"reductions_details":{"rate":0.0},"messages":1,"messages_details":{"rate":0.0},"messages_ready":2,"messages_ready_details":{"rate":0.0},"messages_unacknowledged":3,"messages_unacknowledged_details":{"rate":0.0},"idle_since":"2017-06-30 8:13:31","consumer_utilisation":null,"policy":null,"exclusive_consumer_tag":null,"consumers":1,"recoverable_slaves":null,"state":"running","garbage_collection":{"max_heap_size":0,"min_bin_vheap_size":46422,"min_heap_size":233,"fullsweep_after":65535,"minor_gcs":1},"messages_ram":0,"messages_ready_ram":0,"messages_unacknowledged_ram":0,"messages_persistent":0,"message_bytes":0,"message_bytes_ready":0,"message_bytes_unacknowledged":0,"message_bytes_ram":0,"message_bytes_persistent":0,"head_message_timestamp":null,"disk_reads":0,"disk_writes":0,"backing_queue_status":{"mode":"default","q1":0,"q2":0,"delta":["delta","undefined",0,"undefined"],"q3":0,"q4":0,"len":0,"target_ram_count":"infinity","next_seq_id":0,"avg_ingress_rate":0.0,"avg_egress_rate":0.0,"avg_ack_ingress_rate":0.0,"avg_ack_egress_rate":0.0},"node":"rabbit@050becbb9cb3","arguments":{},"exclusive":false,"auto_delete":false,"durable":true,"vhost":"/","name":"crawl"}]');
        $clock
            ->expects($this->once())
            ->method('at')
            ->with('2017-06-30 8:13:31')
            ->willReturn($date = $this->createMock(PointInTimeInterface::class));

        $queues = $status->queues();

        $this->assertInstanceOf(SetInterface::class, $queues);
        $this->assertSame(Queue::class, (string) $queues->type());
        $this->assertCount(1, $queues);
        $this->assertSame(
            'crawl',
            (string) $queues->current()->identity()->name()
        );
        $this->assertSame(
            '/',
            (string) $queues->current()->identity()->vhost()
        );
        $this->assertSame(
            1,
            $queues->current()->messages()->total()->toInt()
        );
        $this->assertSame(
            2,
            $queues->current()->messages()->ready()->toInt()
        );
        $this->assertSame(
            3,
            $queues->current()->messages()->unacknowledged()->toInt()
        );
        $this->assertSame(
            $date,
            $queues->current()->idleSince()
        );
        $this->assertSame(1, $queues->current()->consumers()->toInt());
        $this->assertSame('running', (string) $queues->current()->state());
        $this->assertSame('rabbit@050becbb9cb3', (string) $queues->current()->node());
        $this->assertFalse($queues->current()->exclusive());
        $this->assertFalse($queues->current()->autoDelete());
        $this->assertTrue($queues->current()->durable());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListQueues()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'queues'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->queues();
    }

    public function testNodes()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'nodes'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"cluster_links":[],"mem_used":65226200,"mem_used_details":{"rate":-1243.2},"fd_used":24,"fd_used_details":{"rate":0.0},"sockets_used":1,"sockets_used_details":{"rate":0.0},"proc_used":245,"proc_used_details":{"rate":0.0},"disk_free":56429953024,"disk_free_details":{"rate":0.0},"io_read_count":1,"io_read_count_details":{"rate":0.0},"io_read_bytes":1,"io_read_bytes_details":{"rate":0.0},"io_read_avg_time":0.677,"io_read_avg_time_details":{"rate":0.0},"io_write_count":0,"io_write_count_details":{"rate":0.0},"io_write_bytes":0,"io_write_bytes_details":{"rate":0.0},"io_write_avg_time":0.0,"io_write_avg_time_details":{"rate":0.0},"io_sync_count":0,"io_sync_count_details":{"rate":0.0},"io_sync_avg_time":0.0,"io_sync_avg_time_details":{"rate":0.0},"io_seek_count":0,"io_seek_count_details":{"rate":0.0},"io_seek_avg_time":0.0,"io_seek_avg_time_details":{"rate":0.0},"io_reopen_count":0,"io_reopen_count_details":{"rate":0.0},"mnesia_ram_tx_count":50,"mnesia_ram_tx_count_details":{"rate":0.0},"mnesia_disk_tx_count":1,"mnesia_disk_tx_count_details":{"rate":0.0},"msg_store_read_count":0,"msg_store_read_count_details":{"rate":0.0},"msg_store_write_count":0,"msg_store_write_count_details":{"rate":0.0},"queue_index_journal_write_count":0,"queue_index_journal_write_count_details":{"rate":0.0},"queue_index_write_count":0,"queue_index_write_count_details":{"rate":0.0},"queue_index_read_count":0,"queue_index_read_count_details":{"rate":0.0},"gc_num":108209,"gc_num_details":{"rate":10.2},"gc_bytes_reclaimed":1268145968,"gc_bytes_reclaimed_details":{"rate":148406.4},"context_switches":469344,"context_switches_details":{"rate":44.6},"io_file_handle_open_attempt_count":10,"io_file_handle_open_attempt_count_details":{"rate":0.0},"io_file_handle_open_attempt_avg_time":0.0837,"io_file_handle_open_attempt_avg_time_details":{"rate":0.0},"partitions":[],"os_pid":"117","fd_total":1048576,"sockets_total":943626,"mem_limit":838356172,"mem_alarm":false,"disk_free_limit":50000000,"disk_free_alarm":false,"proc_total":1048576,"rates_mode":"basic","uptime":7408973,"run_queue":0,"processors":4,"exchange_types":[{"name":"direct","description":"AMQP direct exchange, as per the AMQP specification","enabled":true},{"name":"fanout","description":"AMQP fanout exchange, as per the AMQP specification","enabled":true},{"name":"topic","description":"AMQP topic exchange, as per the AMQP specification","enabled":true},{"name":"headers","description":"AMQP headers exchange, as per the AMQP specification","enabled":true}],"auth_mechanisms":[{"name":"RABBIT-CR-DEMO","description":"RabbitMQ Demo challenge-response authentication mechanism","enabled":false},{"name":"AMQPLAIN","description":"QPid AMQPLAIN mechanism","enabled":true},{"name":"PLAIN","description":"SASL PLAIN authentication mechanism","enabled":true}],"applications":[{"name":"amqp_client","description":"RabbitMQ AMQP Client","version":"3.6.6"},{"name":"asn1","description":"The Erlang ASN1 compiler version 4.0.4","version":"4.0.4"},{"name":"compiler","description":"ERTS  CXC 138 10","version":"7.0.3"},{"name":"crypto","description":"CRYPTO","version":"3.7.2"},{"name":"inets","description":"INETS  CXC 138 49","version":"6.3.4"},{"name":"kernel","description":"ERTS  CXC 138 10","version":"5.1.1"},{"name":"mnesia","description":"MNESIA  CXC 138 12","version":"4.14.2"},{"name":"mochiweb","description":"MochiMedia Web Server","version":"2.13.1"},{"name":"os_mon","description":"CPO  CXC 138 46","version":"2.4.1"},{"name":"public_key","description":"Public key infrastructure","version":"1.3"},{"name":"rabbit","description":"RabbitMQ","version":"3.6.6"},{"name":"rabbit_common","description":"","version":"3.6.6"},{"name":"rabbitmq_management","description":"RabbitMQ Management Console","version":"3.6.6"},{"name":"rabbitmq_management_agent","description":"RabbitMQ Management Agent","version":"3.6.6"},{"name":"rabbitmq_web_dispatch","description":"RabbitMQ Web Dispatcher","version":"3.6.6"},{"name":"ranch","description":"Socket acceptor pool for TCP protocols.","version":"1.2.1"},{"name":"sasl","description":"SASL  CXC 138 11","version":"3.0.2"},{"name":"ssl","description":"Erlang/OTP SSL application","version":"8.1"},{"name":"stdlib","description":"ERTS  CXC 138 10","version":"3.2"},{"name":"syntax_tools","description":"Syntax tools","version":"2.1.1"},{"name":"webmachine","description":"webmachine","version":"1.10.3"},{"name":"xmerl","description":"XML parser","version":"1.3.12"}],"contexts":[{"description":"RabbitMQ Management","path":"/","port":"15672"}],"log_file":"tty","sasl_log_file":"tty","db_dir":"/var/lib/rabbitmq/mnesia/rabbit@050becbb9cb3","config_files":["/etc/rabbitmq/rabbitmq.config"],"net_ticktime":60,"enabled_plugins":["rabbitmq_management"],"name":"rabbit@050becbb9cb3","type":"disc","running":true}]');

        $nodes = $status->nodes();

        $this->assertInstanceOf(SetInterface::class, $nodes);
        $this->assertSame(Node::class, (string) $nodes->type());
        $this->assertCount(1, $nodes);
        $this->assertSame(
            'rabbit@050becbb9cb3',
            (string) $nodes->current()->name()
        );
        $this->assertSame(
            'disc',
            (string) $nodes->current()->type()
        );
        $this->assertTrue($nodes->current()->running());
    }

    public function testRemoteNodes()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class),
            new Remote(new Host('rabbit.innmind.com'))
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'nodes' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));
        $process
            ->expects($this->once())
            ->method('output')
            ->willReturn(
                $output = $this->createMock(Output::class)
            );
        $output
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('[{"cluster_links":[],"mem_used":65226200,"mem_used_details":{"rate":-1243.2},"fd_used":24,"fd_used_details":{"rate":0.0},"sockets_used":1,"sockets_used_details":{"rate":0.0},"proc_used":245,"proc_used_details":{"rate":0.0},"disk_free":56429953024,"disk_free_details":{"rate":0.0},"io_read_count":1,"io_read_count_details":{"rate":0.0},"io_read_bytes":1,"io_read_bytes_details":{"rate":0.0},"io_read_avg_time":0.677,"io_read_avg_time_details":{"rate":0.0},"io_write_count":0,"io_write_count_details":{"rate":0.0},"io_write_bytes":0,"io_write_bytes_details":{"rate":0.0},"io_write_avg_time":0.0,"io_write_avg_time_details":{"rate":0.0},"io_sync_count":0,"io_sync_count_details":{"rate":0.0},"io_sync_avg_time":0.0,"io_sync_avg_time_details":{"rate":0.0},"io_seek_count":0,"io_seek_count_details":{"rate":0.0},"io_seek_avg_time":0.0,"io_seek_avg_time_details":{"rate":0.0},"io_reopen_count":0,"io_reopen_count_details":{"rate":0.0},"mnesia_ram_tx_count":50,"mnesia_ram_tx_count_details":{"rate":0.0},"mnesia_disk_tx_count":1,"mnesia_disk_tx_count_details":{"rate":0.0},"msg_store_read_count":0,"msg_store_read_count_details":{"rate":0.0},"msg_store_write_count":0,"msg_store_write_count_details":{"rate":0.0},"queue_index_journal_write_count":0,"queue_index_journal_write_count_details":{"rate":0.0},"queue_index_write_count":0,"queue_index_write_count_details":{"rate":0.0},"queue_index_read_count":0,"queue_index_read_count_details":{"rate":0.0},"gc_num":108209,"gc_num_details":{"rate":10.2},"gc_bytes_reclaimed":1268145968,"gc_bytes_reclaimed_details":{"rate":148406.4},"context_switches":469344,"context_switches_details":{"rate":44.6},"io_file_handle_open_attempt_count":10,"io_file_handle_open_attempt_count_details":{"rate":0.0},"io_file_handle_open_attempt_avg_time":0.0837,"io_file_handle_open_attempt_avg_time_details":{"rate":0.0},"partitions":[],"os_pid":"117","fd_total":1048576,"sockets_total":943626,"mem_limit":838356172,"mem_alarm":false,"disk_free_limit":50000000,"disk_free_alarm":false,"proc_total":1048576,"rates_mode":"basic","uptime":7408973,"run_queue":0,"processors":4,"exchange_types":[{"name":"direct","description":"AMQP direct exchange, as per the AMQP specification","enabled":true},{"name":"fanout","description":"AMQP fanout exchange, as per the AMQP specification","enabled":true},{"name":"topic","description":"AMQP topic exchange, as per the AMQP specification","enabled":true},{"name":"headers","description":"AMQP headers exchange, as per the AMQP specification","enabled":true}],"auth_mechanisms":[{"name":"RABBIT-CR-DEMO","description":"RabbitMQ Demo challenge-response authentication mechanism","enabled":false},{"name":"AMQPLAIN","description":"QPid AMQPLAIN mechanism","enabled":true},{"name":"PLAIN","description":"SASL PLAIN authentication mechanism","enabled":true}],"applications":[{"name":"amqp_client","description":"RabbitMQ AMQP Client","version":"3.6.6"},{"name":"asn1","description":"The Erlang ASN1 compiler version 4.0.4","version":"4.0.4"},{"name":"compiler","description":"ERTS  CXC 138 10","version":"7.0.3"},{"name":"crypto","description":"CRYPTO","version":"3.7.2"},{"name":"inets","description":"INETS  CXC 138 49","version":"6.3.4"},{"name":"kernel","description":"ERTS  CXC 138 10","version":"5.1.1"},{"name":"mnesia","description":"MNESIA  CXC 138 12","version":"4.14.2"},{"name":"mochiweb","description":"MochiMedia Web Server","version":"2.13.1"},{"name":"os_mon","description":"CPO  CXC 138 46","version":"2.4.1"},{"name":"public_key","description":"Public key infrastructure","version":"1.3"},{"name":"rabbit","description":"RabbitMQ","version":"3.6.6"},{"name":"rabbit_common","description":"","version":"3.6.6"},{"name":"rabbitmq_management","description":"RabbitMQ Management Console","version":"3.6.6"},{"name":"rabbitmq_management_agent","description":"RabbitMQ Management Agent","version":"3.6.6"},{"name":"rabbitmq_web_dispatch","description":"RabbitMQ Web Dispatcher","version":"3.6.6"},{"name":"ranch","description":"Socket acceptor pool for TCP protocols.","version":"1.2.1"},{"name":"sasl","description":"SASL  CXC 138 11","version":"3.0.2"},{"name":"ssl","description":"Erlang/OTP SSL application","version":"8.1"},{"name":"stdlib","description":"ERTS  CXC 138 10","version":"3.2"},{"name":"syntax_tools","description":"Syntax tools","version":"2.1.1"},{"name":"webmachine","description":"webmachine","version":"1.10.3"},{"name":"xmerl","description":"XML parser","version":"1.3.12"}],"contexts":[{"description":"RabbitMQ Management","path":"/","port":"15672"}],"log_file":"tty","sasl_log_file":"tty","db_dir":"/var/lib/rabbitmq/mnesia/rabbit@050becbb9cb3","config_files":["/etc/rabbitmq/rabbitmq.config"],"net_ticktime":60,"enabled_plugins":["rabbitmq_management"],"name":"rabbit@050becbb9cb3","type":"disc","running":true}]');

        $nodes = $status->nodes();

        $this->assertInstanceOf(SetInterface::class, $nodes);
        $this->assertSame(Node::class, (string) $nodes->type());
        $this->assertCount(1, $nodes);
        $this->assertSame(
            'rabbit@050becbb9cb3',
            (string) $nodes->current()->name()
        );
        $this->assertSame(
            'disc',
            (string) $nodes->current()->type()
        );
        $this->assertTrue($nodes->current()->running());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun
     */
    public function testThrowWhenFailToListNodes()
    {
        $status = new Status(
            $server = $this->createMock(Server::class),
            $this->createMock(TimeContinuumInterface::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn(
                $processes = $this->createMock(Processes::class)
            );
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function(Command $command): bool {
                return (string) $command === "rabbitmqadmin '-f' 'raw_json' 'list' 'nodes'";
            }))
            ->willReturn(
                $process = $this->createMock(Process::class)
            );
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn($process);
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $status->nodes();
    }
}
