<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\Peer;
use Innmind\Url\Authority\{
    HostInterface,
    PortInterface
};
use PHPUnit\Framework\TestCase;

class PeerTest extends TestCase
{
    public function testInterface()
    {
        $peer = new Peer(
            $host = $this->createMock(HostInterface::class),
            $port = $this->createMock(PortInterface::class)
        );

        $this->assertSame($host, $peer->host());
        $this->assertSame($port, $peer->port());
    }
}
