<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Channel,
    Channel\Name,
    Channel\Messages,
    VHost\Name as VHostName,
    User\Name as UserName,
    Node\Name as NodeName,
    State,
    Count
};
use Innmind\TimeContinuum\PointInTime;
use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{
    public function testInterface()
    {
        $channel = new Channel(
            $name = new Name('foo'),
            $vhost = new VHostName('foo'),
            $user = new UserName('foo'),
            42,
            $node = new NodeName('rabbit@foo'),
            State::running(),
            $messages = new Messages(
                new Count(0),
                new Count(0),
                new Count(0)
            ),
            $consumers = new Count(0),
            true,
            false,
            $idle = $this->createMock(PointInTime::class)
        );

        $this->assertSame($name, $channel->name());
        $this->assertSame($vhost, $channel->vhost());
        $this->assertSame($user, $channel->user());
        $this->assertSame(42, $channel->number());
        $this->assertSame($node, $channel->node());
        $this->assertSame(State::running(), $channel->state());
        $this->assertSame($messages, $channel->messages());
        $this->assertSame($consumers, $channel->consumers());
        $this->assertTrue($channel->confirm());
        $this->assertFalse($channel->transactional());
        $this->assertSame($idle, $channel->idleSince());
    }
}
