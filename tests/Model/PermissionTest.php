<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Permission,
    User\Name as UserName,
    VHost\Name as VHostName
};
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function testInterface()
    {
        $permission = new Permission(
            $user = new UserName('foo'),
            $vhost = new VHostName('foo'),
            $configure = '.*',
            $write = '.*',
            $read = '.*',
        );

        $this->assertSame($user, $permission->user());
        $this->assertSame($vhost, $permission->vhost());
        $this->assertSame($configure, $permission->configure());
        $this->assertSame($write, $permission->write());
        $this->assertSame($read, $permission->read());
    }
}
