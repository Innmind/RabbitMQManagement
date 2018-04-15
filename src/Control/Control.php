<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\Control as ControlInterface;
use Innmind\Server\Control\Server;

final class Control implements ControlInterface
{
    private $users;
    private $vhosts;
    private $permissions;

    public function __construct(Server $server)
    {
        $this->users = new Users\Users($server);
        $this->vhosts = new VHosts\VHosts($server);
        $this->permissions = new Permissions\Permissions($server);
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
