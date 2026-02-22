<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\VHosts;
use Innmind\Server\Control\{
    Server,
    Server\Process\Builder,
};
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class VHostsTest extends TestCase
{
    public function testDeclare()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'declare' 'vhost' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
        $vhosts = VHosts::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $vhosts->declare('foo')->match(
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
                    "rabbitmqadmin 'declare' 'vhost' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $vhosts = VHosts::of($server);

        $this->assertNull($vhosts->declare('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $server = Server::via(
            function($command) {
                $this->assertSame(
                    "rabbitmqadmin 'delete' 'vhost' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)->build(),
                );
            },
        );
        $vhosts = VHosts::of($server);

        $this->assertInstanceOf(
            SideEffect::class,
            $vhosts->delete('foo')->match(
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
                    "rabbitmqadmin 'delete' 'vhost' 'name=foo'",
                    $command->toString(),
                );

                return Attempt::result(
                    Builder::foreground(2)
                        ->failed()
                        ->build(),
                );
            },
        );
        $vhosts = VHosts::of($server);

        $this->assertNull($vhosts->delete('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
