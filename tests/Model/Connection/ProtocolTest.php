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
            (new Protocol('AMQP 0-9-1 (incl. extensions)'))->toString(),
        );
        $this->assertSame(
            'AMQP 0-9-1',
            (new Protocol('AMQP 0-9-1'))->toString(),
        );
        $this->assertSame(
            'AMQP 0-9',
            (new Protocol('AMQP 0-9'))->toString(),
        );
        $this->assertSame(
            'AMQP 0-8',
            (new Protocol('AMQP 0-8'))->toString(),
        );
    }

    public function testThrowForUnknownProtocol()
    {
        $this->expectException(UnknownProtocol::class);

        new Protocol('foo');
    }
}
