<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Model\Node\Name;
use Innmind\Url\Authority\HostInterface;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $name = new Name('rabbit@whatever');

        $this->assertSame('rabbit@whatever', (string) $name);
        $this->assertInstanceOf(HostInterface::class, $name->host());
        $this->assertSame('whatever', (string) $name->host());
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\InvalidName
     * @expectedExceptionMessage whatever
     */
    public function testThrowWhenInvalidName()
    {
        new Name('whatever');
    }
}
