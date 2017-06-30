<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Exchange;

use Innmind\RabbitMQ\Management\Model\Exchange\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame('foo', (string) new Name('foo'));
    }
}
