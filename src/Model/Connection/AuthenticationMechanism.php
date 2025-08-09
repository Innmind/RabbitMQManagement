<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

/**
 * @psalm-immutable
 */
enum AuthenticationMechanism
{
    case demo;
    case plain;
    case amqplain;

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(string $value): self
    {
        return match ($value) {
            'RABBIT-CR-DEMO' => self::demo,
            'PLAIN' => self::plain,
            'AMQPLAIN' => self::amqplain,
        };
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return match ($this) {
            self::demo => 'RABBIT-CR-DEMO',
            self::plain => 'PLAIN',
            self::amqplain => 'AMQPLAIN',
        };
    }
}
