<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

final class Type
{
    private static $disc;
    private static $ram;

    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function disc(): self
    {
        return self::$disc ?? self::$disc = new self('disc');
    }

    public static function ram(): self
    {
        return self::$ram ?? self::$ram = new self('ram');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
