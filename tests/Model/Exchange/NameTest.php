<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Exchange;

use Innmind\RabbitMQ\Management\Model\Exchange\Name;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame('foo', Name::of('foo')->toString());
    }
}
