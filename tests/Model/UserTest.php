<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User,
    Model\User\Name,
    Model\User\Password,
};
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInterface()
    {
        $user = new User(
            $name = new Name('foo'),
            $password = new Password('foo', 'bar'),
            'foo',
            'bar'
        );

        $this->assertSame($name, $user->name());
        $this->assertSame($password, $user->password());
        $this->assertInstanceOf(Set::class, $user->tags());
        $this->assertSame('string', $user->tags()->type());
        $this->assertSame(['foo', 'bar'], unwrap($user->tags()));
    }
}
