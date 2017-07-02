<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Messages,
    Count
};
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testInterface()
    {
        $messages = new Messages(
            $total = new Count(0),
            $ready = new Count(0),
            $unacknowledged = new Count(0)
        );

        $this->assertSame($total, $messages->total());
        $this->assertSame($ready, $messages->ready());
        $this->assertSame($unacknowledged, $messages->unacknowledged());
    }
}
