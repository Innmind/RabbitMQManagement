<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Channel;

use Innmind\RabbitMQ\Management\Model\{
    Channel\Messages,
    Count
};
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testInterface()
    {
        $messages = new Messages(
            $uncommitted = new Count(0),
            $unconfirmed = new Count(0),
            $unacknowledged = new Count(0),
        );

        $this->assertSame($uncommitted, $messages->uncommitted());
        $this->assertSame($unconfirmed, $messages->unconfirmed());
        $this->assertSame($unacknowledged, $messages->unacknowledged());
    }
}
