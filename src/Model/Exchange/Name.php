<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Exchange;

/**
 * @psalm-immutable
 */
final class Name
{
    private function __construct(private string $value)
    {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(string $value): self
    {
        return new self($value);
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return $this->value;
    }
}
