<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Connection,
    Connection\Name,
    Connection\Timeout,
    Connection\Protocol,
    Connection\AuthenticationMechanism,
    Connection\Peer,
    Connection\Type,
    VHost\Name as VHostName,
    User\Name as UserName,
    Node\Name as NodeName,
    State
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Url\Authority\{
    Host,
    Port
};
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testInterface()
    {
        $connection = new Connection(
            $name = new Name('foo'),
            $connectedAt = $this->createMock(PointInTime::class),
            $timeout = new Timeout(42),
            $vhost = new VHostName('foo'),
            $user = new UserName('foo'),
            $protocol = new Protocol('AMQP 0-9-1'),
            $authenticationMechanism = AuthenticationMechanism::plain(),
            true,
            $peer = new Peer(
                Host::none(),
                Port::none(),
            ),
            $host = Host::none(),
            $port = Port::none(),
            $node = new NodeName('rabbit@foo'),
            $type = Type::network(),
            $state = State::running(),
        );

        $this->assertSame($name, $connection->name());
        $this->assertSame($connectedAt, $connection->connectedAt());
        $this->assertSame($timeout, $connection->timeout());
        $this->assertSame($vhost, $connection->vhost());
        $this->assertSame($user, $connection->user());
        $this->assertSame($protocol, $connection->protocol());
        $this->assertSame($authenticationMechanism, $connection->authenticationMechanism());
        $this->assertTrue($connection->ssl());
        $this->assertSame($peer, $connection->peer());
        $this->assertSame($host, $connection->host());
        $this->assertSame($port, $connection->port());
        $this->assertSame($node, $connection->node());
        $this->assertSame($type, $connection->type());
        $this->assertSame($state, $connection->state());
    }
}
