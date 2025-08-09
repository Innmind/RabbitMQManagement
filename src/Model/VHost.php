<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\VHost\{
    Name,
    Messages,
};

/**
 * @psalm-immutable
 */
final class VHost
{
    private function __construct(
        private Name $name,
        private Messages $messages,
        private bool $tracing,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(Name $name, Messages $messages, bool $tracing): self
    {
        return new self($name, $messages, $tracing);
    }

    #[\NoDiscard]
    public function tracing(): bool
    {
        return $this->tracing;
    }

    #[\NoDiscard]
    public function name(): Name
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function messages(): Messages
    {
        return $this->messages;
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return $this->name->toString();
    }
}
