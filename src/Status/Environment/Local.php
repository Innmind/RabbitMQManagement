<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status\Environment;

use Innmind\RabbitMQ\Management\Status\Environment;
use Innmind\Server\Control\Server\Command;

final class Local implements Environment
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Command $command): Command
    {
        return $command;
    }
}
