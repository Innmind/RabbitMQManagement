<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\{
    Model\Connection\Protocol,
    Exception\UnknownProtocol
};
use PHPUnit\Framework\TestCase;

class ProtocolTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame(
            'AMQP 0-9-1 (incl. extensions)',
            (string) new Protocol('AMQP 0-9-1 (incl. extensions)')
        );
        $this->assertSame(
            'AMQP 0-9-1',
            (string) new Protocol('AMQP 0-9-1')
        );
        $this->assertSame(
            'AMQP 0-9',
            (string) new Protocol('AMQP 0-9')
        );
        $this->assertSame(
            'AMQP 0-8',
            (string) new Protocol('AMQP 0-8')
        );
    }

    public function testThrowForUnknownProtocol()
    {
        $this->expectException(UnknownProtocol::class);

        new Protocol('foo');
    }
}
