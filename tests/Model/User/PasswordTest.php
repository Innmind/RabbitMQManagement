<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\User;

use Innmind\RabbitMQ\Management\Model\User\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testInterface()
    {
        $password = Password::of('foo', 'bar');

        $this->assertSame('foo', $password->hash());
        $this->assertSame('bar', $password->algorithm());
    }
}
