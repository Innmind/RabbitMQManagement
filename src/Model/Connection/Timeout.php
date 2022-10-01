<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

/**
 * @psalm-immutable
 */
final class Timeout
{
    /** @var 0|positive-int */
    private int $value;

    /**
     * @param 0|positive-int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return 0|positive-int
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
