<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Consumer\Tag,
    Channel\Name,
    Queue\Identity
};

final class Consumer
{
    private $tag;
    private $queue;
    private $ackRequired;
    private $exclusive;
    private $channel;

    public function __construct(
        Tag $tag,
        Name $channel,
        Identity $queue,
        bool $ackRequired,
        bool $exclusive
    ) {
        $this->tag = $tag;
        $this->queue = $queue;
        $this->ackRequired = $ackRequired;
        $this->exclusive = $exclusive;
        $this->channel = $channel;
    }

    public function tag(): Tag
    {
        return $this->tag;
    }

    public function queue(): Identity
    {
        return $this->queue;
    }

    public function ackRequired(): bool
    {
        return $this->ackRequired;
    }

    public function exclusive(): bool
    {
        return $this->exclusive;
    }

    public function channel(): Name
    {
        return $this->channel;
    }
}
