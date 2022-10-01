<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Queue,
    Queue\Identity,
    Queue\Messages,
    Count,
    VHost\Name as VHostName,
    Node\Name as NodeName,
    State
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Immutable\Maybe;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    public function testInterface()
    {
        $queue = new Queue(
            $identity = new Identity('foo', new VHostName('foo')),
            $messages = new Messages(
                new Count(0),
                new Count(0),
                new Count(0),
            ),
            $idleSince = Maybe::of($this->createMock(PointInTime::class)),
            $consumers = new Count(0),
            $state = State::running,
            $node = new NodeName('rabbit@foo'),
            true,
            false,
            true,
        );

        $this->assertSame($identity, $queue->identity());
        $this->assertSame($messages, $queue->messages());
        $this->assertSame($idleSince, $queue->idleSince());
        $this->assertSame($consumers, $queue->consumers());
        $this->assertSame($state, $queue->state());
        $this->assertSame($node, $queue->node());
        $this->assertTrue($queue->exclusive());
        $this->assertFalse($queue->autoDelete());
        $this->assertTrue($queue->durable());
    }
}
