<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\Environment;
use Innmind\Server\Control\Server\Command;
use Innmind\Url\{
    Authority\Host,
    Authority\Port,
    Path,
};

final class Remote implements Environment
{
    private Host $host;
    private Port $port;
    private string $username;
    private string $password;
    private ?Path $vhost;

    private function __construct(
        Host $host,
        Port $port,
        string $username,
        string $password,
        ?Path $vhost,
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    #[\Override]
    public function __invoke(Command $command): Command
    {
        if ($this->vhost) {
            $command = $command->withOption('vhost', $this->vhost->toString());
        }

        return $command
            ->withOption('host', $this->host->toString())
            ->withOption('port', $this->port->toString())
            ->withOption('username', $this->username)
            ->withOption('password', $this->password);
    }

    public static function of(
        Host $host,
        ?Port $port = null,
        ?string $username = null,
        ?string $password = null,
        ?Path $vhost = null,
    ): self {
        return new self(
            $host,
            $port ?? Port::of(15672),
            $username ?? 'guest',
            $password ?? 'guest',
            $vhost,
        );
    }
}
