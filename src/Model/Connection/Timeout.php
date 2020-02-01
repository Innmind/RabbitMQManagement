<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Exception\TimeoutCantBeNegative;

final class Timeout
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new TimeoutCantBeNegative((string) $value);
        }

        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
