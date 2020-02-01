<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Consumer;

final class Tag
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
