<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

use Innmind\Server\Control\{
    Server,
    Server\Command,
};
use Innmind\Immutable\{
    Maybe,
    SideEffect,
};

final class VHosts
{
    private Server $server;
    private Command $command;

    private function __construct(Server $server)
    {
        $this->server = $server;
        $this->command = Command::foreground('rabbitmqadmin');
    }

    public static function of(Server $server): self
    {
        return new self($server);
    }

    /**
     * @return Maybe<SideEffect>
     */
    public function declare(string $name): Maybe
    {
        return $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('declare')
                    ->withArgument('vhost')
                    ->withArgument('name='.$name),
            )
            ->wait()
            ->maybe();
    }

    /**
     * @return Maybe<SideEffect>
     */
    public function delete(string $name): Maybe
    {
        return $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('vhost')
                    ->withArgument('name='.$name),
            )
            ->wait()
            ->maybe();
    }
}
