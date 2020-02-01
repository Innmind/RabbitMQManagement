<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model\Connection;

use Innmind\RabbitMQ\Management\{
    Model\Connection\AuthenticationMechanism,
    Exception\UnknownAuthenticationMechanism,
};
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
        $this->assertSame($expected, $mechanism->toString());
    }

    public function mechanisms(): array
    {
        return [
            ['demo', 'RABBIT-CR-DEMO'],
            ['plain', 'PLAIN'],
            ['amqplain', 'AMQPLAIN'],
        ];
    }

    public function testThrowWhenUnknownMechanism()
    {
        $this->expectException(UnknownAuthenticationMechanism::class);

        AuthenticationMechanism::of('foo');
    }
}
