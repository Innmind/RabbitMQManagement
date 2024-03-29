<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\User;

use Innmind\RabbitMQ\Management\Model\User\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame('foo', Name::of('foo')->toString());
    }
}
