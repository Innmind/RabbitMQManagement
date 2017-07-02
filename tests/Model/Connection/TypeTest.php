<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testNetwork()
    {
        $type = Type::network();

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('network', (string) $type);
        $this->assertSame($type, Type::network());
    }

    public function testDirect()
    {
        $type = Type::direct();

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('direct', (string) $type);
        $this->assertSame($type, Type::direct());
    }
}
