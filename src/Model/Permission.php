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
    #[\NoDiscard]
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

    #[\NoDiscard]
    public function user(): User\Name
    {
        return $this->user;
    }

    #[\NoDiscard]
    public function vhost(): VHost\Name
    {
        return $this->vhost;
    }

    #[\NoDiscard]
    public function configure(): string
    {
        return $this->configure;
    }

    #[\NoDiscard]
    public function write(): string
    {
        return $this->write;
    }

    #[\NoDiscard]
    public function read(): string
    {
        return $this->read;
    }
}
