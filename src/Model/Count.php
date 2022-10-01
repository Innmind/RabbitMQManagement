<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

/**
 * @psalm-immutable
 */
final class Count
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
