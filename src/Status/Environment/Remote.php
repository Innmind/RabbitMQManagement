<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\Environment;
use Innmind\Server\Control\Server\Command;
use Innmind\Url\Authority\{
    HostInterface,
    PortInterface,
    Port
};

final class Remote implements Environment
{
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct(
        HostInterface $host,
        PortInterface $port = null,
        string $username = 'guest',
        string $password = 'guest'
    ) {
        $this->host = $host;
        $this->port = $port ?? new Port(15672);
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Command $command): Command
    {
        return $command
            ->withOption('host', (string) $this->host)
            ->withOption('port', (string) $this->port)
            ->withOption('username', $this->username)
            ->withOption('password', $this->password);
    }
}
