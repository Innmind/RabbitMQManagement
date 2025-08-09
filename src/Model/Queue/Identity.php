<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\VHost\Name;

/**
 * @psalm-immutable
 */
final class Identity
{
    private function __construct(
        private string $name,
        private Name $vhost,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(string $name, Name $vhost): self
    {
        return new self($name, $vhost);
    }

    #[\NoDiscard]
    public function name(): string
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function vhost(): Name
    {
        return $this->vhost;
    }
}
