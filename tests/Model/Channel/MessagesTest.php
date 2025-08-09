<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Channel;

use Innmind\RabbitMQ\Management\Model\{
    Channel\Messages,
    Count
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testInterface()
    {
        $messages = Messages::of(
            $uncommitted = Count::of(0),
            $unconfirmed = Count::of(0),
            $unacknowledged = Count::of(0),
        );

        $this->assertSame($uncommitted, $messages->uncommitted());
        $this->assertSame($unconfirmed, $messages->unconfirmed());
        $this->assertSame($unacknowledged, $messages->unacknowledged());
    }
}
