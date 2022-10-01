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

    public function __construct(Server $server)
    {
        $this->users = new Users($server);
        $this->vhosts = new VHosts($server);
        $this->permissions = new Permissions($server);
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
