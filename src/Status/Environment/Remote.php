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

    public function __construct(
        Host $host,
        Port $port = null,
        string $username = null,
        string $password = null,
        Path $vhost = null
    ) {
        $this->host = $host;
        $this->port = $port ?? Port::of(15672);
        $this->username = $username ?? 'guest';
        $this->password = $password ?? 'guest';
        $this->vhost = $vhost;
    }

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
}
