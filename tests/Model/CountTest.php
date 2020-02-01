<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\Count,
    Exception\CountCantBeNegative
};
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame(0, (new Count(0))->toInt());
        $this->assertSame(42, (new Count(42))->toInt());
    }

    public function testThrowWhenNegativeCount()
    {
        $this->expectException(CountCantBeNegative::class);

        new Count(-1);
    }
}
