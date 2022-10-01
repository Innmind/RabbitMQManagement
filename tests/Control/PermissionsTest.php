<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\{
    Control\Permissions,
    Exception\ManagementPluginFailedToRun,
};
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

class PermissionsTest extends TestCase
{
    public function testDeclare()
    {
        $permissions = new Permissions(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

        $this->assertNull(
            $permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}'),
        );
    }

    public function testThrowWhenFailToDeclare()
    {
        $permissions = new Permissions(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->expectException(ManagementPluginFailedToRun::class);

        $permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}');
    }

    public function testDelete()
    {
        $permissions = new Permissions(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

        $this->assertNull($permissions->delete('/', 'foo'));
    }

    public function testThrowWhenFailToDelete()
    {
        $permissions = new Permissions(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->expectException(ManagementPluginFailedToRun::class);

        $permissions->delete('/', 'foo');
    }
}
