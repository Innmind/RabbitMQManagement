<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

interface Users
{
    public function declare(string $name, string $password, string ...$tags): void;
    public function delete(string $name): void;
}
