<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

final class State
{
    private static $running;
    private static $idle;

    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function running(): self
    {
        return self::$running ?? self::$running = new self('running');
    }

    public static function idle(): self
    {
        return self::$idle ?? self::$idle = new self('idle');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
