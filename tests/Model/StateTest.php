<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    public function testRunning()
    {
        $state = State::running();

        $this->assertInstanceOf(State::class, $state);
        $this->assertSame('running', $state->toString());
        $this->assertSame($state, State::running());
    }

    public function testIdle()
    {
        $state = State::idle();

        $this->assertInstanceOf(State::class, $state);
        $this->assertSame('idle', $state->toString());
        $this->assertSame($state, State::idle());
    }
}
