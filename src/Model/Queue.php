<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Queue\Identity,
    Queue\Messages,
    Node\Name as NodeName
};
use Innmind\TimeContinuum\PointInTimeInterface;

final class Queue
{
    private $identity;
    private $messages;
    private $idleSince;
    private $consumers;
    private $state;
    private $node;
    private $exclusive;
    private $autoDelete;
    private $durable;

    public function __construct(
        Identity $identity,
        Messages $messages,
        PointInTimeInterface $idleSince,
        Count $consumers,
        State $state,
        NodeName $node,
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

    public function idleSince(): PointInTimeInterface
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

    public function node(): NodeName
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
