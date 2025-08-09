<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

/**
 * @psalm-immutable
 */
enum Protocol
{
    case v091IncludingExtensions;
    case v091;
    case v09;
    case v08;

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(string $value): self
    {
        return match ($value) {
            'AMQP 0-9-1 (incl. extensions)' => self::v091IncludingExtensions,
            'AMQP 0-9-1' => self::v091,
            'AMQP 0-9' => self::v09,
            'AMQP 0-8' => self::v08,
        };
    }
}
