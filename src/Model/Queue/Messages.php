<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Queue;

use Innmind\RabbitMQ\Management\Model\Count;

/**
 * @psalm-immutable
 */
final class Messages
{
    private function __construct(
        private Count $total,
        private Count $ready,
        private Count $unacknowledged,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(
        Count $total,
        Count $ready,
        Count $unacknowledged,
    ): self {
        return new self($total, $ready, $unacknowledged);
    }

    #[\NoDiscard]
    public function total(): Count
    {
        return $this->total;
    }

    #[\NoDiscard]
    public function ready(): Count
    {
        return $this->ready;
    }

    #[\NoDiscard]
    public function unacknowledged(): Count
    {
        return $this->unacknowledged;
    }
}
