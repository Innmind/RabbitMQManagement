<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Identity,
    VHost\Name
};
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Identity(
            'foo',
            $vhost = new Name('bar'),
        );

        $this->assertSame('foo', $identity->name());
        $this->assertSame($vhost, $identity->vhost());
    }
}
