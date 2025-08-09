<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Consumer;

/**
 * @psalm-immutable
 */
final class Tag
{
    private function __construct(private string $value)
    {
    }

    /**
     * @psalm-pure
     */
    public static function of(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
