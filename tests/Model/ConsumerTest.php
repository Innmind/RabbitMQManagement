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
use PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{
    public function testInterface()
    {
        $consumer = new Consumer(
            $tag = new Tag('foo'),
            $channel = new ChannelName('foo'),
            $queue = new Identity('foo', new VHostName('foo')),
            $connection = new ConnectionName('foo'),
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
