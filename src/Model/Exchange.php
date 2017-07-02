<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Exchange\Name,
    Exchange\Type,
    VHost\Name as VHostName
};

final class Exchange
{
    private $name;
    private $vhost;
    private $type;
    private $durable;
    private $autoDelete;
    private $internal;

    public function __construct(
        Name $name,
        VHostName $vhost,
        Type $type,
        bool $durable,
        bool $autoDelete,
        bool $internal
    ) {
        $this->name = $name;
        $this->vhost = $vhost;
        $this->type = $type;
        $this->durable = $durable;
        $this->autoDelete = $autoDelete;
        $this->internal = $internal;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function vhost(): VHostName
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
