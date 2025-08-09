<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management;

use Innmind\RabbitMQ\Management\Control\{
    Users,
    VHosts,
    Permissions,
};
use Innmind\Server\Control\Server;

final class Control
{
    private function __construct(
        private Users $users,
        private VHosts $vhosts,
        private Permissions $permissions,
    ) {
    }

    #[\NoDiscard]
    public static function of(Server $server): self
    {
        return new self(
            Users::of($server),
            VHosts::of($server),
            Permissions::of($server),
        );
    }

    #[\NoDiscard]
    public function users(): Users
    {
        return $this->users;
    }

    #[\NoDiscard]
    public function vhosts(): VHosts
    {
        return $this->vhosts;
    }

    #[\NoDiscard]
    public function permissions(): Permissions
    {
        return $this->permissions;
    }
}
