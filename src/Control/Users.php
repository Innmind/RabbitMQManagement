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

final class Users
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
    public function declare(string $name, string $password, string ...$tags): Maybe
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
                    ->withArgument('user')
                    ->withArgument('name='.$name),
            )
            ->wait()
            ->maybe();
    }
}
