<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Exchange;

use Innmind\RabbitMQ\Management\Model\Exchange\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @dataProvider types
     */
    public function testInterface($type)
    {
        $instance = Type::{$type}();

        $this->assertInstanceOf(Type::class, $instance);
        $this->assertSame($type, $instance->toString());
        $this->assertSame($instance, Type::{$type}());
    }

    public function types(): array
    {
        return [
            ['direct'],
            ['topic'],
            ['headers'],
            ['fanout'],
        ];
    }
}
