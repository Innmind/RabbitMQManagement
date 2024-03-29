<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\{
    Environment\Remote,
    Environment
};
use Innmind\Server\Control\Server\Command;
use Innmind\Url\{
    Authority\Host,
    Authority\Port,
    Path,
};
use PHPUnit\Framework\TestCase;

class RemoteTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Environment::class,
            Remote::of(
                Host::none(),
            ),
        );
    }

    public function testInvokation()
    {
        $command = Command::foreground('rabbitmqadmin')
            ->withShortOption('f', 'raw_json')
            ->withArgument('list')
            ->withArgument('users');
        $environment = Remote::of(Host::of('rabbit.innmind.com'));

        $this->assertNotSame($command, $environment($command));
        $this->assertInstanceOf(Command::class, $environment($command));
        $this->assertSame(
            "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'",
            $environment($command)->toString(),
        );
    }

    public function testInvokationWithCustomSerevr()
    {
        $command = Command::foreground('rabbitmqadmin')
            ->withShortOption('f', 'raw_json')
            ->withArgument('list')
            ->withArgument('users');
        $environment = Remote::of(
            Host::of('rabbit.innmind.com'),
            Port::of(42),
            'foo',
            'bar',
        );

        $this->assertNotSame($command, $environment($command));
        $this->assertInstanceOf(Command::class, $environment($command));
        $this->assertSame(
            "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--host=rabbit.innmind.com' '--port=42' '--username=foo' '--password=bar'",
            $environment($command)->toString(),
        );
    }

    public function testInvokationWithVhost()
    {
        $command = Command::foreground('rabbitmqadmin')
            ->withShortOption('f', 'raw_json')
            ->withArgument('list')
            ->withArgument('users');
        $environment = Remote::of(
            Host::of('rabbit.innmind.com'),
            null,
            null,
            null,
            Path::of('/foo'),
        );

        $this->assertNotSame($command, $environment($command));
        $this->assertInstanceOf(Command::class, $environment($command));
        $this->assertSame(
            "rabbitmqadmin '-f' 'raw_json' 'list' 'users' '--vhost=/foo' '--host=rabbit.innmind.com' '--port=15672' '--username=guest' '--password=guest'",
            $environment($command)->toString(),
        );
    }
}
