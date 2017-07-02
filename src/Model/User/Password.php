<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\User;

final class Password
{
    private $hash;
    private $algorithm;

    public function __construct(string $hash, string $algorithm)
    {
        $this->hash = $hash;
        $this->algorithm = $algorithm;
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
