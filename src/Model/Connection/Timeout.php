<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

/**
 * @psalm-immutable
 */
final class Timeout
{
    /**
     * @param int<0, max> $value
     */
    private function __construct(private int $value)
    {
    }

    /**
     * @psalm-pure
     *
     * @param int<0, max> $value
     */
    #[\NoDiscard]
    public static function of(int $value): self
    {
        return new self($value);
    }

    /**
     * @return int<0, max>
     */
    #[\NoDiscard]
    public function toInt(): int
    {
        return $this->value;
    }
}
