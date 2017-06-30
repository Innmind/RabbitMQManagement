<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\Timeout;
use PHPUnit\Framework\TestCase;

class TimeoutTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame(0, (new Timeout(0))->toInt());
        $this->assertSame(42, (new Timeout(42))->toInt());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\TimeoutCantBeNegative
     */
    public function testThrowWhenNegativeTimeout()
    {
        new Timeout(-1);
    }
}
