<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\Users;
use Innmind\Server\Control\Servers\Mock;
use Innmind\Immutable\SideEffect;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    public function testDeclare()
    {
        $server = Mock::new($this->assert())
            ->willExecute(fn($command) => $this->assertSame(
                "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'",
                $command->toString(),
            ));
        $users = Users::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $users->declare('foo', 'bar', 'baz', 'foobar')->match(
                static fn($sideEffect) => $sideEffect,
                static fn() => null,
            ),
        );
    }

    public function testReturnNothingWhenFailToDeclare()
    {
        $server = Mock::new($this->assert())
            ->willExecute(
                fn($command) => $this->assertSame(
                    "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'",
                    $command->toString(),
                ),
                static fn($_, $builder) => $builder->failed(),
            );
        $users = Users::of($server);

        $this->assertNull($users->declare('foo', 'bar', 'baz', 'foobar')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $server = Mock::new($this->assert())
            ->willExecute(fn($command) => $this->assertSame(
                "rabbitmqadmin 'delete' 'user' 'name=foo'",
                $command->toString(),
            ));
        $users = Users::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $users->delete('foo')->match(
                static fn($sideEffect) => $sideEffect,
                static fn() => null,
            ),
        );
    }

    public function testReturnNothingWhenFailToDelete()
    {
        $server = Mock::new($this->assert())
            ->willExecute(
                fn($command) => $this->assertSame(
                    "rabbitmqadmin 'delete' 'user' 'name=foo'",
                    $command->toString(),
                ),
                static fn($_, $builder) => $builder->failed(),
            );
        $users = Users::of($server);

        $this->assertNull($users->delete('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
