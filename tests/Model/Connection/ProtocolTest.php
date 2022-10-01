<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\Protocol;
use PHPUnit\Framework\TestCase;

class ProtocolTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame(
            Protocol::v091IncludingExtensions,
            Protocol::of('AMQP 0-9-1 (incl. extensions)'),
        );
        $this->assertSame(
            Protocol::v091,
            Protocol::of('AMQP 0-9-1'),
        );
        $this->assertSame(
            Protocol::v09,
            Protocol::of('AMQP 0-9'),
        );
        $this->assertSame(
            Protocol::v08,
            Protocol::of('AMQP 0-8'),
        );
    }
}
