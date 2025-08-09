<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

/**
 * @psalm-immutable
 */
final class Permission
{
    private function __construct(
        private User\Name $user,
        private VHost\Name $vhost,
        private string $configure,
        private string $write,
        private string $read,
    ) {
    }

    /**
     * @psalm-pure
     */
    public static function of(
        User\Name $user,
        VHost\Name $vhost,
        string $configure,
        string $write,
        string $read,
    ): self {
        return new self(
            $user,
            $vhost,
            $configure,
            $write,
            $read,
        );
    }

    public function user(): User\Name
    {
        return $this->user;
    }

    public function vhost(): VHost\Name
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
