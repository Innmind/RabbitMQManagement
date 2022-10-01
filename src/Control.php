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
    private Users $users;
    private VHosts $vhosts;
    private Permissions $permissions;

    private function __construct(Server $server)
    {
        $this->users = Users::of($server);
        $this->vhosts = VHosts::of($server);
        $this->permissions = Permissions::of($server);
    }

    public static function of(Server $server): self
    {
        return new self($server);
    }

    public function users(): Users
    {
        return $this->users;
    }

    public function vhosts(): VHosts
    {
        return $this->vhosts;
    }

    public function permissions(): Permissions
    {
        return $this->permissions;
    }
}
