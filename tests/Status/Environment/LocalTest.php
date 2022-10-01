<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Environment;

use Innmind\RabbitMQ\Management\Status\{
    Environment\Local,
    Environment
};
use Innmind\Server\Control\Server\Command;
use Innmind\Url\Path;
use PHPUnit\Framework\TestCase;

class LocalTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Environment::class,
            Local::of(),
        );
    }

    public function testInvokation()
    {
        $command = Command::foreground('rabbitmqadmin');

        $this->assertSame($command, Local::of()($command));
    }

    public function testInvokationWithVhost()
    {
        $command = Command::foreground('rabbitmqadmin');

        $this->assertSame(
            "rabbitmqadmin '--vhost=/foo'",
            Local::of(Path::of('/foo'))($command)->toString(),
        );
    }
}
