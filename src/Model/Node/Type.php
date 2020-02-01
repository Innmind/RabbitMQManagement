<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

final class Type
{
    private static ?self $disc = null;
    private static ?self $ram = null;

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function disc(): self
    {
        return self::$disc ??= new self('disc');
    }

    public static function ram(): self
    {
        return self::$ram ??= new self('ram');
    }

    public function toString(): string
    {
        return $this->value;
    }
}
