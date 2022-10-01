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

    private function __construct(Host $host, Port $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @psalm-pure
     */
    public static function of(Host $host, Port $port): self
    {
        return new self($host, $port);
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
