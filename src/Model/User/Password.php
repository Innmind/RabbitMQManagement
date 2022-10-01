<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\User;

/**
 * @psalm-immutable
 */
final class Password
{
    private string $hash;
    private string $algorithm;

    private function __construct(string $hash, string $algorithm)
    {
        $this->hash = $hash;
        $this->algorithm = $algorithm;
    }

    /**
     * @psalm-pure
     */
    public static function of(string $hash, string $algorithm): self
    {
        return new self($hash, $algorithm);
    }

    public function algorithm(): string
    {
        return $this->algorithm;
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
