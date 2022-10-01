<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Consumer\Tag,
    Channel\Name,
    Queue\Identity,
};

/**
 * @psalm-immutable
 */
final class Consumer
{
    private Tag $tag;
    private Name $channel;
    private Identity $queue;
    private Connection\Name $connection;
    private bool $ackRequired;
    private bool $exclusive;

    private function __construct(
        Tag $tag,
        Name $channel,
        Identity $queue,
        Connection\Name $connection,
        bool $ackRequired,
        bool $exclusive,
    ) {
        $this->tag = $tag;
        $this->channel = $channel;
        $this->queue = $queue;
        $this->connection = $connection;
        $this->ackRequired = $ackRequired;
        $this->exclusive = $exclusive;
    }

    /**
     * @psalm-pure
     */
    public static function of(
        Tag $tag,
        Name $channel,
        Identity $queue,
        Connection\Name $connection,
        bool $ackRequired,
        bool $exclusive,
    ): self {
        return new self(
            $tag,
            $channel,
            $queue,
            $connection,
            $ackRequired,
            $exclusive,
        );
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

    public function connection(): Connection\Name
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
