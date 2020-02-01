<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    User\Name as UserName,
    VHost\Name as VHostName
};

final class Permission
{
    private UserName $user;
    private VHostName $vhost;
    private string $configure;
    private string $write;
    private string $read;

    public function __construct(
        UserName $user,
        VHostName $vhost,
        string $configure,
        string $write,
        string $read
    ) {
        $this->user = $user;
        $this->vhost = $vhost;
        $this->configure = $configure;
        $this->write = $write;
        $this->read = $read;
    }

    public function user(): UserName
    {
        return $this->user;
    }

    public function vhost(): VHostName
    {
        return $this->vhost;
    }

    public function configure(): string
    {
        return $this->configure;
    }

    public function write(): string
    {
        return $this->write;
    }

    public function read(): string
    {
        return $this->read;
    }
}
