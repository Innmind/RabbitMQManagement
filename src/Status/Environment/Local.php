<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\Environment;
use Innmind\Server\Control\Server\Command;
use Innmind\Url\Path;

final class Local implements Environment
{
    private ?Path $vhost;

    public function __construct(Path $vhost = null)
    {
        $this->vhost = $vhost;
    }

    public function __invoke(Command $command): Command
    {
        if ($this->vhost) {
            return $command->withOption('vhost', $this->vhost->toString());
        }

        return $command;
    }
}
