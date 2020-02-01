<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\Model\Connection\AuthenticationMechanism;
use PHPUnit\Framework\TestCase;

class AuthenticationMechanismTest extends TestCase
{
    /**
     * @dataProvider mechanisms
     */
    public function testInterface($type, $expected)
    {
        $mechanism = AuthenticationMechanism::{$type}();

        $this->assertInstanceOf(AuthenticationMechanism::class, $mechanism);
        $this->assertSame($mechanism, AuthenticationMechanism::{$type}());
        $this->assertSame($mechanism, AuthenticationMechanism::of($expected));
        $this->assertSame($expected, (string) $mechanism);
    }

    public function mechanisms(): array
    {
        return [
            ['demo', 'RABBIT-CR-DEMO'],
            ['plain', 'PLAIN'],
            ['amqplain', 'AMQPLAIN'],
        ];
    }

    /**
     * @expectedException Innmind\RabbitMQ\Management\Exception\UnknownAuthenticationMechanism
     */
    public function throwWhenUnknownMechanism()
    {
        AuthenticationMechanism::of('foo');
    }
}
