<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

/**
 * @psalm-immutable
 */
enum State
{
    case running;
    case idle;
}
