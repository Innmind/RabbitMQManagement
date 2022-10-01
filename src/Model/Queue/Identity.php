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

    private function __construct(string $name, Name $vhost)
    {
        $this->name = $name;
        $this->vhost = $vhost;
    }

    /**
     * @psalm-pure
     */
    public static function of(string $name, Name $vhost): self
    {
        return new self($name, $vhost);
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
