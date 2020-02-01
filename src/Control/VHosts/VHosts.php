<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control\VHosts;

use Innmind\RabbitMQ\Management\{
    Control\VHosts as VHostsInterface,
    Exception\ManagementPluginFailedToRun
};
use Innmind\Server\Control\{
    Server,
    Server\Command
};

final class VHosts implements VHostsInterface
{
    private Server $server;
    private Command $command;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->command = Command::foreground('rabbitmqadmin');
    }

    public function declare(string $name): VHostsInterface
    {
        $exitCode = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('declare')
                    ->withArgument('vhost')
                    ->withArgument('name='.$name)
            )
            ->wait()
            ->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return $this;
    }

    public function delete(string $name): VHostsInterface
    {
        $exitCode = $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('vhost')
                    ->withArgument('name='.$name)
            )
            ->wait()
            ->exitCode();

        if (!$exitCode->isSuccessful()) {
            throw new ManagementPluginFailedToRun;
        }

        return $this;
    }
}
