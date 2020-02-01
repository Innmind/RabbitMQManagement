<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\VHost;

use Innmind\RabbitMQ\Management\Model\VHost\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame('foo', (new Name('foo'))->toString());
    }
}
