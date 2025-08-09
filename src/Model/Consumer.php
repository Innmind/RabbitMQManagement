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
    private function __construct(
        private Tag $tag,
        private Name $channel,
        private Identity $queue,
        private Connection\Name $connection,
        private bool $ackRequired,
        private bool $exclusive,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
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

    #[\NoDiscard]
    public function tag(): Tag
    {
        return $this->tag;
    }

    #[\NoDiscard]
    public function channel(): Name
    {
        return $this->channel;
    }

    #[\NoDiscard]
    public function queue(): Identity
    {
        return $this->queue;
    }

    #[\NoDiscard]
    public function connection(): Connection\Name
    {
        return $this->connection;
    }

    #[\NoDiscard]
    public function ackRequired(): bool
    {
        return $this->ackRequired;
    }

    #[\NoDiscard]
    public function exclusive(): bool
    {
        return $this->exclusive;
    }
}
