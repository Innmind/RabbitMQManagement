<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\Environment;
use Innmind\Server\Control\Server\Command;
use Innmind\Url\Authority\{
    Host,
    Port,
};

final class Remote implements Environment
{
    private Host $host;
    private Port $port;
    private string $username;
    private string $password;

    public function __construct(
        Host $host,
        Port $port = null,
        string $username = 'guest',
        string $password = 'guest'
    ) {
        $this->host = $host;
        $this->port = $port ?? Port::of(15672);
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Command $command): Command
    {
        return $command
            ->withOption('host', $this->host->toString())
            ->withOption('port', $this->port->toString())
            ->withOption('username', $this->username)
            ->withOption('password', $this->password);
    }
}
