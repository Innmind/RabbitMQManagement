<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Exception\ManagementPluginFailedToRun;
use Innmind\Server\Control\{
    Server,
    Server\Command,
};

final class Users
{
    private Server $server;
    private Command $command;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->command = Command::foreground('rabbitmqadmin');
    }

    public function declare(string $name, string $password, string ...$tags): void
    {
        $_ = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('declare')
                    ->withArgument('user')
                    ->withArgument('name='.$name)
                    ->withArgument('password='.$password)
                    ->withArgument('tags='.\implode(',', $tags)),
            )
            ->wait()
            ->match(
                static fn() => null, // successful
                static fn() => throw new ManagementPluginFailedToRun,
            );
    }

    public function delete(string $name): void
    {
        $_ = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('user')
                    ->withArgument('name='.$name),
            )
            ->wait()
            ->match(
                static fn() => null, // successful
                static fn() => throw new ManagementPluginFailedToRun,
            );
    }
}
