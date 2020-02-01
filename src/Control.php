<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management;

use Innmind\RabbitMQ\Management\Control\{
    Users,
    VHosts,
    Permissions,
};

interface Control
{
    public function users(): Users;
    public function vhosts(): VHosts;
    public function permissions(): Permissions;
}
