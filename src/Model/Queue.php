<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Identity,
    Queue\Messages,
};
use Innmind\TimeContinuum\PointInTime;

final class Queue
{
    private Identity $identity;
    private Messages $messages;
    private PointInTime $idleSince;
    private Count $consumers;
    private State $state;
    private Node\Name $node;
    private bool $exclusive;
    private bool $autoDelete;
    private bool $durable;

    public function __construct(
        Identity $identity,
        Messages $messages,
        PointInTime $idleSince,
        Count $consumers,
        State $state,
        Node\Name $node,
        bool $exclusive,
        bool $autoDelete,
        bool $durable
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

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    public function idleSince(): PointInTime
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
