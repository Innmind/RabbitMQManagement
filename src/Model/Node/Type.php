<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

/**
 * @psalm-immutable
 */
enum Type
{
    case disc;
    case ram;
}
