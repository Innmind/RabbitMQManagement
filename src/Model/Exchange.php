<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Exchange\Name,
    Exchange\Type,
};

/**
 * @psalm-immutable
 */
final class Exchange
{
    private function __construct(
        private Name $name,
        private VHost\Name $vhost,
        private Type $type,
        private bool $durable,
        private bool $autoDelete,
        private bool $internal,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(
        Name $name,
        VHost\Name $vhost,
        Type $type,
        bool $durable,
        bool $autoDelete,
        bool $internal,
    ): self {
        return new self(
            $name,
            $vhost,
            $type,
            $durable,
            $autoDelete,
            $internal,
        );
    }

    #[\NoDiscard]
    public function name(): Name
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function vhost(): VHost\Name
    {
        return $this->vhost;
    }

    #[\NoDiscard]
    public function type(): Type
    {
        return $this->type;
    }

    #[\NoDiscard]
    public function durable(): bool
    {
        return $this->durable;
    }

    #[\NoDiscard]
    public function autoDelete(): bool
    {
        return $this->autoDelete;
    }

    #[\NoDiscard]
    public function internal(): bool
    {
        return $this->internal;
    }
}
