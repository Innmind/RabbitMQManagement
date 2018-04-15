<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Control;

use Innmind\RabbitMQ\Management\{
    Control\Control,
    Control as ControlInterface,
    Control\Users\Users,
    Control\VHosts\VHosts,
    Control\Permissions\Permissions
};
use Innmind\Server\Control\Server;
use PHPUnit\Framework\TestCase;

class ControlTest extends TestCase
{
    public function testInterface()
    {
        $control = new Control($this->createMock(Server::class));

        $this->assertInstanceOf(ControlInterface::class, $control);
        $this->assertInstanceOf(Users::class, $control->users());
        $this->assertInstanceOf(VHosts::class, $control->vhosts());
        $this->assertInstanceOf(Permissions::class, $control->permissions());
    }
}
