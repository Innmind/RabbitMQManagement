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
    private Name $name;
    private VHost\Name $vhost;
    private Type $type;
    private bool $durable;
    private bool $autoDelete;
    private bool $internal;

    private function __construct(
        Name $name,
        VHost\Name $vhost,
        Type $type,
        bool $durable,
        bool $autoDelete,
        bool $internal,
    ) {
        $this->name = $name;
        $this->vhost = $vhost;
        $this->type = $type;
        $this->durable = $durable;
        $this->autoDelete = $autoDelete;
        $this->internal = $internal;
    }

    /**
     * @psalm-pure
     */
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

    public function name(): Name
    {
        return $this->name;
    }

    public function vhost(): VHost\Name
    {
        return $this->vhost;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function durable(): bool
    {
        return $this->durable;
    }

    public function autoDelete(): bool
    {
        return $this->autoDelete;
    }

    public function internal(): bool
    {
        return $this->internal;
    }
}
