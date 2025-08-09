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

final class Users
{
    private function __construct(
        private Server $server,
        private Command $command,
    ) {
    }

    public static function of(Server $server): self
    {
        return new self($server, Command::foreground('rabbitmqadmin'));
    }

    /**
     * @return Attempt<SideEffect>
     */
    public function declare(string $name, string $password, string ...$tags): Attempt
    {
        return $this
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
            ->flatMap(static fn($process) => $process->wait()->attempt(
                static fn($error) => new \RuntimeException($error::class),
            ))
            ->map(static fn() => new SideEffect);
    }

    /**
     * @return Attempt<SideEffect>
     */
    public function delete(string $name): Attempt
    {
        return $this
            ->server
            ->processes()
            ->execute(
                $this
                    ->command
                    ->withArgument('delete')
                    ->withArgument('user')
                    ->withArgument('name='.$name),
            )
            ->flatMap(static fn($process) => $process->wait()->attempt(
                static fn($error) => new \RuntimeException($error::class),
            ))
            ->map(static fn() => new SideEffect);
    }
}
