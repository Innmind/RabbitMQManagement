<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Channel;

use Innmind\RabbitMQ\Management\Model\Count;

/**
 * @psalm-immutable
 */
final class Messages
{
    private Count $uncommitted;
    private Count $unconfirmed;
    private Count $unacknowledged;

    private function __construct(
        Count $uncommitted,
        Count $unconfirmed,
        Count $unacknowledged,
    ) {
        $this->uncommitted = $uncommitted;
        $this->unconfirmed = $unconfirmed;
        $this->unacknowledged = $unacknowledged;
    }

    /**
     * @psalm-pure
     */
    public static function of(
        Count $uncommitted,
        Count $unconfirmed,
        Count $unacknowledged,
    ): self {
        return new self($uncommitted, $unconfirmed, $unacknowledged);
    }

    public function uncommitted(): Count
    {
        return $this->uncommitted;
    }

    public function unconfirmed(): Count
    {
        return $this->unconfirmed;
    }

    public function unacknowledged(): Count
    {
        return $this->unacknowledged;
    }
}
