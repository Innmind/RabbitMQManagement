<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\VHost\Name;

/**
 * @psalm-immutable
 */
final class Identity
{
    private string $name;
    private Name $vhost;

    public function __construct(string $name, Name $vhost)
    {
        $this->name = $name;
        $this->vhost = $vhost;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function vhost(): Name
    {
        return $this->vhost;
    }
}
