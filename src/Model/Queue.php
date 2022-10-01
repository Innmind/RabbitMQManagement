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
    private Identity $identity;
    private Messages $messages;
    /** @var Maybe<PointInTime> */
    private Maybe $idleSince;
    private Count $consumers;
    private State $state;
    private Node\Name $node;
    private bool $exclusive;
    private bool $autoDelete;
    private bool $durable;

    /**
     * @param Maybe<PointInTime> $idleSince
     */
    private function __construct(
        Identity $identity,
        Messages $messages,
        Maybe $idleSince,
        Count $consumers,
        State $state,
        Node\Name $node,
        bool $exclusive,
        bool $autoDelete,
        bool $durable,
    ) {
        $this->identity = $identity;
        $this->messages = $messages;
        $this->idleSince = $idleSince;
        $this->consumers = $consumers;
        $this->state = $state;
        $this->node = $node;
        $this->exclusive = $exclusive;
        $this->autoDelete = $autoDelete;
        $this->durable = $durable;
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<PointInTime> $idleSince
     */
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

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    /**
     * @return Maybe<PointInTime>
     */
    public function idleSince(): Maybe
    {
        return $this->idleSince;
    }

    public function consumers(): Count
    {
        return $this->consumers;
    }

    public function state(): State
    {
        return $this->state;
    }

    public function node(): Node\Name
    {
        return $this->node;
    }

    public function exclusive(): bool
    {
        return $this->exclusive;
    }

    public function autoDelete(): bool
    {
        return $this->autoDelete;
    }

    public function durable(): bool
    {
        return $this->durable;
    }
}
