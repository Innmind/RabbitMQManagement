<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User,
    Model\User\Name,
    Model\User\Password,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInterface()
    {
        $user = new User(
            $name = new Name('foo'),
            $password = new Password('foo', 'bar'),
            $tags = new Set('string')
        );

        $this->assertSame($name, $user->name());
        $this->assertSame($password, $user->password());
        $this->assertSame($tags, $user->tags());
    }

    public function testThrowWhenInvalidTags()
    {
        $this->expectException(InvalidArgumentException::class);

        new User(
            new Name('foo'),
            new Password('foo', 'bar'),
            new Set('int')
        );
    }
}
