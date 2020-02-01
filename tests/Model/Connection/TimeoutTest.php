<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\{
    Model\Connection\Timeout,
    Exception\TimeoutCantBeNegative
};
use PHPUnit\Framework\TestCase;

class TimeoutTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame(0, (new Timeout(0))->toInt());
        $this->assertSame(42, (new Timeout(42))->toInt());
    }

    public function testThrowWhenNegativeTimeout()
    {
        $this->expectException(TimeoutCantBeNegative::class);

        new Timeout(-1);
    }
}
