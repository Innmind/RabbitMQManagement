<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Messages,
    Count
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testInterface()
    {
        $messages = Messages::of(
            $total = Count::of(0),
            $ready = Count::of(0),
            $unacknowledged = Count::of(0),
        );

        $this->assertSame($total, $messages->total());
        $this->assertSame($ready, $messages->ready());
        $this->assertSame($unacknowledged, $messages->unacknowledged());
    }
}
