<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\VHost;

use Innmind\RabbitMQ\Management\Model\Count;

final class Messages
{
    private $total;
    private $ready;
    private $unacknowledged;

    public function __construct(
        Count $total,
        Count $ready,
        Count $unacknowledged
    ) {
        $this->total = $total;
        $this->ready = $ready;
        $this->unacknowledged = $unacknowledged;
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
