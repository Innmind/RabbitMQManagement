<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    VHost,
    VHost\Name
};
use PHPUnit\Framework\TestCase;

class VHostTest extends TestCase
{
    public function testInterface()
    {
        $vhost = new VHost(
            $name = new Name('foo'),
            true
        );

        $this->assertSame($name, $vhost->name());
        $this->assertSame('foo', (string) $vhost);
        $this->assertTrue($vhost->tracing());
    }
}
