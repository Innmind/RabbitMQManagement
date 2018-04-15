<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

interface VHosts
{
    public function declare(string $name): self;
    public function delete(string $name): self;
}
