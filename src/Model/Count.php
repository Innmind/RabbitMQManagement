<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

/**
 * @psalm-immutable
 */
final class Count
{
    /**
     * @param 0|positive-int $value
     */
    private function __construct(private int $value)
    {
    }

    /**
     * @param 0|positive-int $value
     */
    public static function of(int $value): self
    {
        return new self($value);
    }

    /**
     * @return 0|positive-int
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
