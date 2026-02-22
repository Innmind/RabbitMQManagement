<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\Users;
use Innmind\Server\Control\{
    Server,
    Server\Process\Builder,
};
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    public function testDeclare()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
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
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $users = Users::of($server);

        $this->assertNull($users->declare('foo', 'bar', 'baz', 'foobar')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'delete' 'user' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
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
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'delete' 'user' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $users = Users::of($server);

        $this->assertNull($users->delete('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
