<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\Permissions;
use Innmind\Server\Control\{
    Server,
    Server\Process\Builder,
};
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class PermissionsTest extends TestCase
{
    public function testDeclare()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
        $permissions = Permissions::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}')->match(
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
                    "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $permissions = Permissions::of($server);

        $this->assertNull($permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
        $permissions = Permissions::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $permissions->delete('/', 'foo')->match(
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
                    "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $permissions = Permissions::of($server);

        $this->assertNull($permissions->delete('/', 'foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
