<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\{
    Control\Users,
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

class UsersTest extends TestCase
{
    public function testDeclare()
    {
        $users = Users::of(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

        $this->assertNull($users->declare('foo', 'bar', 'baz', 'foobar'));
    }

    public function testThrowWhenFailToDeclare()
    {
        $users = Users::of(
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
                return $command->toString() === "rabbitmqadmin 'declare' 'user' 'name=foo' 'password=bar' 'tags=baz,foobar'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->expectException(ManagementPluginFailedToRun::class);

        $users->declare('foo', 'bar', 'baz', 'foobar');
    }

    public function testDelete()
    {
        $users = Users::of(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'user' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::right(new SideEffect));

        $this->assertNull($users->delete('foo'));
    }

    public function testThrowWhenFailToDelete()
    {
        $users = Users::of(
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
                return $command->toString() === "rabbitmqadmin 'delete' 'user' 'name=foo'";
            }))
            ->willReturn($process = $this->createMock(Process::class));
        $process
            ->expects($this->once())
            ->method('wait')
            ->willReturn(Either::left(new ExitCode(1)));

        $this->expectException(ManagementPluginFailedToRun::class);

        $users->delete('foo');
    }
}
