<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

interface Permissions
{
    public function declare(
        string $vhost,
        string $user,
        string $configure,
        string $write,
        string $read
    ): self;
    public function delete(string $vhost, string $user): self;
}
