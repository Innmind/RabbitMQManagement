<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Exception\UnknownAuthenticationMechanism;

final class AuthenticationMechanism
{
    private const DEMO = 'RABBIT-CR-DEMO';
    private const PLAIN = 'PLAIN';
    private const AMQPLAIN = 'AMQPLAIN';

    private static $demo;
    private static $plain;
    private static $amqplain;

    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function demo(): self
    {
        return self::$demo ?? self::$demo = new self(self::DEMO);
    }

    public static function plain(): self
    {
        return self::$plain ?? self::$plain = new self(self::PLAIN);
    }

    public static function amqplain(): self
    {
        return self::$amqplain ?? self::$amqplain = new self(self::AMQPLAIN);
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::DEMO:
                return self::demo();

            case self::PLAIN:
                return self::plain();

            case self::AMQPLAIN:
                return self::amqplain();

            default:
                throw new UnknownAuthenticationMechanism;
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
