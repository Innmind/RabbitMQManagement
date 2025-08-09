<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Identity,
    Queue\Messages,
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Queue
{
    /**
     * @param Maybe<PointInTime> $idleSince
     */
    private function __construct(
        private Identity $identity,
        private Messages $messages,
        private Maybe $idleSince,
        private Count $consumers,
        private State $state,
        private Node\Name $node,
        private bool $exclusive,
        private bool $autoDelete,
        private bool $durable,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<PointInTime> $idleSince
     */
    #[\NoDiscard]
    public static function of(
        Identity $identity,
        Messages $messages,
        Maybe $idleSince,
        Count $consumers,
        State $state,
        Node\Name $node,
        bool $exclusive,
        bool $autoDelete,
        bool $durable,
    ): self {
        return new self(
            $identity,
            $messages,
            $idleSince,
            $consumers,
            $state,
            $node,
            $exclusive,
            $autoDelete,
            $durable,
        );
    }

    #[\NoDiscard]
    public function identity(): Identity
    {
        return $this->identity;
    }

    #[\NoDiscard]
    public function messages(): Messages
    {
        return $this->messages;
    }

    /**
     * @return Maybe<PointInTime>
     */
    #[\NoDiscard]
    public function idleSince(): Maybe
    {
        return $this->idleSince;
    }

    #[\NoDiscard]
    public function consumers(): Count
    {
        return $this->consumers;
    }

    #[\NoDiscard]
    public function state(): State
    {
        return $this->state;
    }

    #[\NoDiscard]
    public function node(): Node\Name
    {
        return $this->node;
    }

    #[\NoDiscard]
    public function exclusive(): bool
    {
        return $this->exclusive;
    }

    #[\NoDiscard]
    public function autoDelete(): bool
    {
        return $this->autoDelete;
    }

    #[\NoDiscard]
    public function durable(): bool
    {
        return $this->durable;
    }
}
