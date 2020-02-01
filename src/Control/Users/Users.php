<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control\Users;

use Innmind\RabbitMQ\Management\{
    Control\Users as UsersInterface,
    Exception\ManagementPluginFailedToRun,
};
use Innmind\Server\Control\{
    Server,
    Server\Command,
};

final class Users implements UsersInterface
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
        $process = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('declare')
                    ->withArgument('user')
                    ->withArgument('name='.$name)
                    ->withArgument('password='.$password)
                    ->withArgument('tags='.implode(',', $tags)),
            );
        $process->wait();
        $exitCode = $process->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }
    }

    public function delete(string $name): void
    {
        $process = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('user')
                    ->withArgument('name='.$name),
            );
        $process->wait();
        $exitCode = $process->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }
    }
}
