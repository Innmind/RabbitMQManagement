<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\Peer;
use Innmind\Url\Authority\{
    Host,
    Port
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class PeerTest extends TestCase
{
    public function testInterface()
    {
        $peer = Peer::of(
            $host = Host::none(),
            $port = Port::none(),
        );

        $this->assertSame($host, $peer->host());
        $this->assertSame($port, $peer->port());
    }
}
