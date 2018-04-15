<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Environment;

use Innmind\RabbitMQ\Management\Status\{
    Environment\Remote,
    Environment
};
use Innmind\Server\Control\Server\Command;
use Innmind\Url\Authority\{
    HostInterface,
    Host,
    Port
};
use PHPUnit\Framework\TestCase;

class RemoteTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Environment::class,
            new Remote(
                $this->createMock(HostInterface::class)
            )
        );
    }

    public function testInvokation()
    {
        $command = (new Command('rabbitmqadmin'))
            ->withShortOption('f', 'raw_json')
            ->withArgument('list')
            ->withArgument('users');
        $environment = new Remote(new Host('rabbit.innmind.com'));

        $this->assertNotSame($command, $environment($command));
        $this->assertInstanceOf(Command::class, $environment($command));
        $this->assertSame(
            "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'",
            (string) $environment($command)
        );
    }

    public function testInvokationWithCustomSerevr()
    {
        $command = (new Command('rabbitmqadmin'))
            ->withShortOption('f', 'raw_json')
            ->withArgument('list')
            ->withArgument('users');
        $environment = new Remote(
            new Host('rabbit.innmind.com'),
            new Port(42),
            'foo',
            'bar'
        );

        $this->assertNotSame($command, $environment($command));
        $this->assertInstanceOf(Command::class, $environment($command));
        $this->assertSame(
            "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--host=rabbit.innmind.com' '--port=42' '--username=foo' '--password=bar'",
            (string) $environment($command)
        );
    }
}
