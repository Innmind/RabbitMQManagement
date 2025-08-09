<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    VHost,
    VHost\Name,
    VHost\Messages,
    Count
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class VHostTest extends TestCase
{
    public function testInterface()
    {
        $vhost = VHost::of(
            $name = Name::of('foo'),
            $messages = Messages::of(
                Count::of(0),
                Count::of(0),
                Count::of(0),
            ),
            true,
        );

        $this->assertSame($name, $vhost->name());
        $this->assertSame($messages, $vhost->messages());
        $this->assertSame('foo', $vhost->toString());
        $this->assertTrue($vhost->tracing());
    }
}
