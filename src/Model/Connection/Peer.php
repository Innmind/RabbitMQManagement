<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\Url\Authority\{
    Host,
    Port,
};

/**
 * @psalm-immutable
 */
final class Peer
{
    private Host $host;
    private Port $port;

    public function __construct(Host $host, Port $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function host(): Host
    {
        return $this->host;
    }

    public function port(): Port
    {
        return $this->port;
    }
}
