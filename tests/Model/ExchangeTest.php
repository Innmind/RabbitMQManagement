<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Exchange,
    Exchange\Name,
    Exchange\Type,
    VHost\Name as VHostName
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ExchangeTest extends TestCase
{
    public function testInterface()
    {
        $exchange = Exchange::of(
            $name = Name::of('foo'),
            $vhost = VHostName::of('foo'),
            $type = Type::fanout,
            true,
            false,
            true,
        );

        $this->assertSame($name, $exchange->name());
        $this->assertSame($vhost, $exchange->vhost());
        $this->assertSame($type, $exchange->type());
        $this->assertTrue($exchange->durable());
        $this->assertFalse($exchange->autoDelete());
        $this->assertTrue($exchange->internal());
    }
}
