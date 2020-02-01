<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Exception\CountCantBeNegative;

final class Count
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new CountCantBeNegative((string) $value);
        }

        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
