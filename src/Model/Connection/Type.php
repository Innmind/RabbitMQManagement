<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

final class Type
{
    private static ?self $network = null;
    private static ?self $direct = null;

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function network(): self
    {
        return self::$network ?? self::$network = new self('network');
    }

    public static function direct(): self
    {
        return self::$direct ?? self::$direct = new self('direct');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
