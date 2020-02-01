<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Exception\UnknownProtocol;

final class Protocol
{
    private static array $allowed = [
        'AMQP 0-9-1 (incl. extensions)',
        'AMQP 0-9-1',
        'AMQP 0-9',
        'AMQP 0-8',
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::$allowed, true)) {
            throw new UnknownProtocol($value);
        }

        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
