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
use Innmind\Immutable\Maybe;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{
    public function testInterface()
    {
        $channel = Channel::of(
            $name = Name::of('foo'),
            $vhost = VHostName::of('foo'),
            $user = UserName::of('foo'),
            42,
            $node = NodeName::of('rabbit@foo'),
            State::running,
            $messages = Messages::of(
                Count::of(0),
                Count::of(0),
                Count::of(0),
            ),
            $consumers = Count::of(0),
            true,
            false,
            $idle = Maybe::of(PointInTime::now()),
        );

        $this->assertSame($name, $channel->name());
        $this->assertSame($vhost, $channel->vhost());
        $this->assertSame($user, $channel->user());
        $this->assertSame(42, $channel->number());
        $this->assertSame($node, $channel->node());
        $this->assertSame(State::running, $channel->state());
        $this->assertSame($messages, $channel->messages());
        $this->assertSame($consumers, $channel->consumers());
        $this->assertTrue($channel->confirm());
        $this->assertFalse($channel->transactional());
        $this->assertSame($idle, $channel->idleSince());
    }
}
