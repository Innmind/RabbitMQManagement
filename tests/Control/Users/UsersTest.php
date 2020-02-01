<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control\Users;

use Innmind\RabbitMQ\Management\{
    Control\Users\Users,
    Control\Users as UsersInterface,
    Exception\ManagementPluginFailedToRun
};
use Innmind\Server\Control\{
    Server,
    Server\Processes,
    Server\Process,
    Server\Process\ExitCode
};
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            UsersInterface::class,
            new Users($this->createMock(Server::class))
        );
    }

    public function testDeclare()
    {
        $users = new Users(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertSame($users, $users->declare('foo', 'bar', 'baz', 'foobar'));
    }

    public function testThrowWhenFailToDeclare()
    {
        $users = new Users(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $this->expectException(ManagementPluginFailedToRun::class);

        $users->declare('foo', 'bar', 'baz', 'foobar');
    }

    public function testDelete()
    {
        $users = new Users(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'user' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(0));

        $this->assertSame($users, $users->delete('foo'));
    }

    public function testThrowWhenFailToDelete()
    {
        $users = new Users(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'user' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait');
        $process
            ->expects($this->once())
            ->method('exitCode')
            ->willReturn(new ExitCode(1));

        $this->expectException(ManagementPluginFailedToRun::class);

        $users->delete('foo');
    }
}
