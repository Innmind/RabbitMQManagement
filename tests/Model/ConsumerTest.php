<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Consumer,
    Consumer\Tag,
    Channel\Name as ChannelName,
    Queue\Identity,
    VHost\Name as VHostName,
    Connection\Name as ConnectionName
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{
    public function testInterface()
    {
        $consumer = Consumer::of(
            $tag = Tag::of('foo'),
            $channel = ChannelName::of('foo'),
            $queue = Identity::of('foo', VHostName::of('foo')),
            $connection = ConnectionName::of('foo'),
            true,
            false,
        );

        $this->assertSame($tag, $consumer->tag());
        $this->assertSame($channel, $consumer->channel());
        $this->assertSame($queue, $consumer->queue());
        $this->assertSame($connection, $consumer->connection());
        $this->assertTrue($consumer->ackRequired());
        $this->assertFalse($consumer->exclusive());
    }
}
