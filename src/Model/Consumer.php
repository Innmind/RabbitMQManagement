<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Consumer\Tag,
    Channel\Name,
    Queue\Identity,
    Connection\Name as ConnectionName
};

final class Consumer
{
    private $tag;
    private $channel;
    private $queue;
    private $connection;
    private $ackRequired;
    private $exclusive;

    public function __construct(
        Tag $tag,
        Name $channel,
        Identity $queue,
        ConnectionName $connection,
        bool $ackRequired,
        bool $exclusive
    ) {
        $this->tag = $tag;
        $this->channel = $channel;
        $this->queue = $queue;
        $this->connection = $connection;
        $this->ackRequired = $ackRequired;
        $this->exclusive = $exclusive;
    }

    public function tag(): Tag
    {
        return $this->tag;
    }

    public function channel(): Name
    {
        return $this->channel;
    }

    public function queue(): Identity
    {
        return $this->queue;
    }

    public function connection(): ConnectionName
    {
        return $this->connection;
    }

    public function ackRequired(): bool
    {
        return $this->ackRequired;
    }

    public function exclusive(): bool
    {
        return $this->exclusive;
    }
}
