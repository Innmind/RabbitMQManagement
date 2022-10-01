<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Connection;

/**
 * @psalm-immutable
 */
enum Type
{
    case network;
    case direct;
}
