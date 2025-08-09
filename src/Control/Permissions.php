<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

use Innmind\Server\Control\{
    Server,
    Server\Command,
};
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};

final class Permissions
{
    private function __construct(
        private Server $server,
        private Command $command,
    ) {
    }

    #[\NoDiscard]
    public static function of(Server $server): self
    {
        return new self($server, Command::foreground('rabbitmqadmin'));
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function declare(
        string $vhost,
        string $user,
        string $configure,
        string $write,
        string $read,
    ): Attempt {
        return $this
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
                    ->withArgument('read='.$read),
            )
            ->flatMap(static fn($process) => $process->wait()->attempt(
                static fn($error) => new \RuntimeException($error::class),
            ))
            ->map(static fn() => new SideEffect);
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function delete(string $vhost, string $user): Attempt
    {
        return $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('permission')
                    ->withArgument('vhost='.$vhost)
                    ->withArgument('user='.$user),
            )
            ->flatMap(static fn($process) => $process->wait()->attempt(
                static fn($error) => new \RuntimeException($error::class),
            ))
            ->map(static fn() => new SideEffect);
    }
}
