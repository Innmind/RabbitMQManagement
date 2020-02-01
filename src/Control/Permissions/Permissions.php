<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control\Permissions;

use Innmind\RabbitMQ\Management\{
    Control\Permissions as PermissionsInterface,
    Exception\ManagementPluginFailedToRun
};
use Innmind\Server\Control\{
    Server,
    Server\Command
};

final class Permissions implements PermissionsInterface
{
    private Server $server;
    private Command $command;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->command = Command::foreground('rabbitmqadmin');
    }

    public function declare(
        string $vhost,
        string $user,
        string $configure,
        string $write,
        string $read
    ): PermissionsInterface {
        $exitCode = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('declare')
                    ->withArgument('permission')
                    ->withArgument('vhost='.$vhost)
                    ->withArgument('user='.$user)
                    ->withArgument('configure='.$configure)
                    ->withArgument('write='.$write)
                    ->withArgument('read='.$read)
            )
            ->wait()
            ->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return $this;
    }

    public function delete(string $vhost, string $user): PermissionsInterface
    {
        $exitCode = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('permission')
                    ->withArgument('vhost='.$vhost)
                    ->withArgument('user='.$user)
            )
            ->wait()
            ->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return $this;
    }
}
