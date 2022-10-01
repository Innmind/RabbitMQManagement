<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\VHost;

use Innmind\RabbitMQ\Management\Model\Count;

/**
 * @psalm-immutable
 */
final class Messages
{
    private Count $total;
    private Count $ready;
    private Count $unacknowledged;

    private function __construct(
        Count $total,
        Count $ready,
        Count $unacknowledged,
    ) {
        $this->total = $total;
        $this->ready = $ready;
        $this->unacknowledged = $unacknowledged;
    }

    /**
     * @psalm-pure
     */
    public static function of(
        Count $total,
        Count $ready,
        Count $unacknowledged,
    ): self {
        return new self($total, $ready, $unacknowledged);
    }

    public function total(): Count
    {
        return $this->total;
    }

    public function ready(): Count
    {
        return $this->ready;
    }

    public function unacknowledged(): Count
    {
        return $this->unacknowledged;
    }
}
