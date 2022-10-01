<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    VHost,
    VHost\Name,
    VHost\Messages,
    Count
};
use PHPUnit\Framework\TestCase;

class VHostTest extends TestCase
{
    public function testInterface()
    {
        $vhost = new VHost(
            $name = new Name('foo'),
            $messages = new Messages(
                new Count(0),
                new Count(0),
                new Count(0),
            ),
            true,
        );

        $this->assertSame($name, $vhost->name());
        $this->assertSame($messages, $vhost->messages());
        $this->assertSame('foo', $vhost->toString());
        $this->assertTrue($vhost->tracing());
    }
}
