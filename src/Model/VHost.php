<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\VHost\{
    Name,
    Messages
};

final class VHost
{
    private $name;
    private $messages;
    private $tracing;

    public function __construct(
        Name $name,
        Messages $messages,
        bool $tracing
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

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
