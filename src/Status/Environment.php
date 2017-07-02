<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Status;

use Innmind\Server\Control\Server\Command;

interface Environment
{
    /**
     * Configure the command so it runs in the expected environment
     */
    public function __invoke(Command $command): Command;
}
