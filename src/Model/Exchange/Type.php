<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Exchange;

/**
 * @psalm-immutable
 */
enum Type
{
    case topic;
    case headers;
    case direct;
    case fanout;
}
