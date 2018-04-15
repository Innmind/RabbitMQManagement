<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control\Permissions;

use Innmind\RabbitMQ\Management\{
    Control\Permissions\Permissions,
    Control\Permissions as PermissionsInterface,
    Exception\ManagementPluginFailedToRun
};
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Process,
    Server\Process\ExitCode
};
use PHPUnit\Framework\TestCase;

class PermissionsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            PermissionsInterface::class,
            new Permissions($this->createMock(Server::class))
        );
    }

    public function testDeclare()
    {
        $permissions = new Permissions(
            $server = $this->createMock(Server::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return (string) $command === "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertSame(
            $permissions,
            $permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}')
        );
    }

    public function testThrowWhenFailToDeclare()
    {
        $permissions = new Permissions(
            $server = $this->createMock(Server::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return (string) $command === "rabbitmqadmin 'declare' 'permission' 'vhost=/' 'user=foo' 'configure=.{1}' 'write=.{2}' 'read=.{3}'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $this->expectException(ManagementPluginFailedToRun::class);

        $permissions->declare('/', 'foo', '.{1}', '.{2}', '.{3}');
    }

    public function testDelete()
    {
        $permissions = new Permissions(
            $server = $this->createMock(Server::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return (string) $command === "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertSame($permissions, $permissions->delete('/', 'foo'));
    }

    public function testThrowWhenFailToDelete()
    {
        $permissions = new Permissions(
            $server = $this->createMock(Server::class)
        );
        $server
            ->expects($this->once())
            ->method('processes')
            ->willReturn($processes = $this->createMock(Processes::class));
        $processes
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($command): bool {
                return (string) $command === "rabbitmqadmin 'delete' 'permission' 'vhost=/' 'user=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->will($this->returnSelf());
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $this->expectException(ManagementPluginFailedToRun::class);

        $permissions->delete('/', 'foo');
    }
}
