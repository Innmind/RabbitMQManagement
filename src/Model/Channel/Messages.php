<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Channel;

use Innmind\RabbitMQ\Management\Model\Count;

/**
 * @psalm-immutable
 */
final class Messages
{
    private function __construct(
        private Count $uncommitted,
        private Count $unconfirmed,
        private Count $unacknowledged,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(
        Count $uncommitted,
        Count $unconfirmed,
        Count $unacknowledged,
    ): self {
        return new self($uncommitted, $unconfirmed, $unacknowledged);
    }

    #[\NoDiscard]
    public function uncommitted(): Count
    {
        return $this->uncommitted;
    }

    #[\NoDiscard]
    public function unconfirmed(): Count
    {
        return $this->unconfirmed;
    }

    #[\NoDiscard]
    public function unacknowledged(): Count
    {
        return $this->unacknowledged;
    }
}
