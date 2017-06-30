<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Consumer;

use Innmind\RabbitMQ\Management\Model\Consumer\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testInterface()
    {
        $this->assertSame('foo', (string) new Tag('foo'));
    }
}
