<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\Node\{
    Name,
    Type
};

final class Node
{
    private Name $name;
    private Type $type;
    private bool $running;

    public function __construct(
        Name $name,
        Type $type,
        bool $running
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->running = $running;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function running(): bool
    {
        return $this->running;
    }
}
