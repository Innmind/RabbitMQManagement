<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\VHosts;
use Innmind\Server\Control\Servers\Mock;
use Innmind\Immutable\SideEffect;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class VHostsTest extends TestCase
{
    public function testDeclare()
    {
        $server = Mock::new($this->assert())
            ->willExecute(fn($command) => $this->assertSame(
                "rabbitmqadmin 'declare' 'vhost' 'name=foo'",
                $command->toString(),
            ));
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
        $server = Mock::new($this->assert())
            ->willExecute(
                fn($command) => $this->assertSame(
                    "rabbitmqadmin 'declare' 'vhost' 'name=foo'",
                    $command->toString(),
                ),
                static fn($_, $builder) => $builder->failed(),
            );
        $vhosts = VHosts::of($server);

        $this->assertNull($vhosts->declare('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $server = Mock::new($this->assert())
            ->willExecute(fn($command) => $this->assertSame(
                "rabbitmqadmin 'delete' 'vhost' 'name=foo'",
                $command->toString(),
            ));
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
        $server = Mock::new($this->assert())
            ->willExecute(
                fn($command) => $this->assertSame(
                    "rabbitmqadmin 'delete' 'vhost' 'name=foo'",
                    $command->toString(),
                ),
                static fn($_, $builder) => $builder->failed(),
            );
        $vhosts = VHosts::of($server);

        $this->assertNull($vhosts->delete('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
