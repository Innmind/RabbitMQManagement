<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\User;

/**
 * @psalm-immutable
 */
final class Password
{
    private function __construct(
        private string $hash,
        private string $algorithm,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(string $hash, string $algorithm): self
    {
        return new self($hash, $algorithm);
    }

    #[\NoDiscard]
    public function algorithm(): string
    {
        return $this->algorithm;
    }

    #[\NoDiscard]
    public function hash(): string
    {
        return $this->hash;
    }
}
