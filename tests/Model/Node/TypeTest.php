<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Model\Node\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testDisc()
    {
        $type = Type::disc();

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('disc', $type->toString());
        $this->assertSame($type, Type::disc());
    }

    public function testRam()
    {
        $type = Type::ram();

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('ram', $type->toString());
        $this->assertSame($type, Type::ram());
    }
}
