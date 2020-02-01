<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Exchange;

final class Type
{
    private static ?self $topic = null;
    private static ?self $headers = null;
    private static ?self $direct = null;
    private static ?self $fanout = null;

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function topic(): self
    {
        return self::$topic ?? self::$topic = new self('topic');
    }

    public static function headers(): self
    {
        return self::$headers ?? self::$headers = new self('headers');
    }

    public static function direct(): self
    {
        return self::$direct ?? self::$direct = new self('direct');
    }

    public static function fanout(): self
    {
        return self::$fanout ?? self::$fanout = new self('fanout');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
