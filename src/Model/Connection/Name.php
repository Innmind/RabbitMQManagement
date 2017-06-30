<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

final class Name
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
