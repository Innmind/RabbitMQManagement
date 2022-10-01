<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\VHost\{
    Name,
    Messages,
};

final class VHost
{
    private Name $name;
    private Messages $messages;
    private bool $tracing;

    public function __construct(
        Name $name,
        Messages $messages,
        bool $tracing,
    ) {
        $this->name = $name;
        $this->messages = $messages;
        $this->tracing = $tracing;
    }

    public function tracing(): bool
    {
        return $this->tracing;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    public function toString(): string
    {
        return $this->name->toString();
    }
}
