<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management;

use Innmind\RabbitMQ\Management\{
    Control,
    Control\Users,
    Control\VHosts,
    Control\Permissions,
};
use Innmind\Server\Control\Servers\Mock;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ControlTest extends TestCase
{
    public function testInterface()
    {
        $control = Control::of(Mock::new($this->assert()));

        $this->assertInstanceOf(Users::class, $control->users());
        $this->assertInstanceOf(VHosts::class, $control->vhosts());
        $this->assertInstanceOf(Permissions::class, $control->permissions());
    }
}
