<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

final class State
{
    private static ?self $running = null;
    private static ?self $idle = null;

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function running(): self
    {
        return self::$running ??= new self('running');
    }

    public static function idle(): self
    {
        return self::$idle ??= new self('idle');
    }

    public function toString(): string
    {
        return $this->value;
    }
}
