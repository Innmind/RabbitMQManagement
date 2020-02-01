<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\{
    Model\Node\Name,
    Exception\InvalidName,
};
use Innmind\Url\Authority\Host;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        $name = new Name('rabbit@whatever');

        $this->assertSame('rabbit@whatever', $name->toString());
        $this->assertInstanceOf(Host::class, $name->host());
        $this->assertSame('whatever', $name->host()->toString());
    }

    public function testThrowWhenInvalidName()
    {
        $this->expectException(InvalidName::class);
        $this->expectExceptionMessage('whatever');

        new Name('whatever');
    }
}
