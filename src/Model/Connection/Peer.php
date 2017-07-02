<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\Url\Authority\{
    HostInterface,
    PortInterface
};

final class Peer
{
    private $host;
    private $port;

    public function __construct(HostInterface $host, PortInterface $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function host(): HostInterface
    {
        return $this->host;
    }

    public function port(): PortInterface
    {
        return $this->port;
    }
}
