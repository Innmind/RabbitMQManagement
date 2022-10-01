<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management;

use Innmind\RabbitMQ\Management\{
    Control,
    Control\Users,
    Control\VHosts,
    Control\Permissions,
};
use Innmind\Server\Control\Server;
use PHPUnit\Framework\TestCase;

class ControlTest extends TestCase
{
    public function testInterface()
    {
        $control = new Control($this->createMock(Server::class));

        $this->assertInstanceOf(Users::class, $control->users());
        $this->assertInstanceOf(VHosts::class, $control->vhosts());
        $this->assertInstanceOf(Permissions::class, $control->permissions());
    }
}
