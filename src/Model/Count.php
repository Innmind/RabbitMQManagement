<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

/**
 * @psalm-immutable
 */
final class Count
{
    /**
     * @param int<0, max> $value
     */
    private function __construct(private int $value)
    {
    }

    /**
     * @param int<0, max> $value
     */
    public static function of(int $value): self
    {
        return new self($value);
    }

    /**
     * @return int<0, max>
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
