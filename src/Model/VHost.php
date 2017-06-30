<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\VHost\Name;

final class VHost
{
    private $name;
    private $tracing;

    public function __construct(Name $name, bool $tracing)
    {
        $this->name = $name;
        $this->tracing = $tracing;
    }

    public function tracing(): bool
    {
        return $this->tracing;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
