<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

interface VHosts
{
    public function declare(string $name): void;
    public function delete(string $name): void;
}
