<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User,
    Model\User\Name,
    Model\User\Password,
};
use Innmind\Immutable\Set;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInterface()
    {
        $user = User::of(
            $name = Name::of('foo'),
            $password = Password::of('foo', 'bar'),
            'foo',
            'bar',
        );

        $this->assertSame($name, $user->name());
        $this->assertSame($password, $user->password());
        $this->assertInstanceOf(Set::class, $user->tags());
        $this->assertSame(['foo', 'bar'], $user->tags()->toList());
    }
}
