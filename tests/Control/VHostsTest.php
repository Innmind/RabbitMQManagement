<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control\VHosts;
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Process,
    Server\Process\ExitCode
};
use Innmind\Immutable\{
    Either,
    SideEffect,
};
use PHPUnit\Framework\TestCase;

class VHostsTest extends TestCase
{
    public function testDeclare()
    {
        $vhosts = VHosts::of(
            $server = $this->createMock(Server::class),
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "rabbitmqadmin 'declare' 'vhost' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

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
        $vhosts = VHosts::of(
            $server = $this->createMock(Server::class),
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "rabbitmqadmin 'declare' 'vhost' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->assertNull($vhosts->declare('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }

    public function testDelete()
    {
        $vhosts = VHosts::of(
            $server = $this->createMock(Server::class),
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "rabbitmqadmin 'delete' 'vhost' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

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
        $vhosts = VHosts::of(
            $server = $this->createMock(Server::class),
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return $command->toString() === "rabbitmqadmin 'delete' 'vhost' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->assertNull($vhosts->delete('foo')->match(
            static fn($sideEffect) => $sideEffect,
            static fn() => null,
        ));
    }
}
