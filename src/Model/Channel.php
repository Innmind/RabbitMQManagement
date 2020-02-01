<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Channel\Name,
    Channel\Messages,
    Node\Name as NodeName,
    VHost\Name as VHostName,
    User\Name as UserName
};
use Innmind\TimeContinuum\PointInTime;

final class Channel
{
    private Name $name;
    private VHostName $vhost;
    private UserName $user;
    private int $number;
    private NodeName $node;
    private State $state;
    private Messages $messages;
    private Count $consumers;
    private bool $confirm;
    private bool $transactional;
    private PointInTime $idleSince;

    public function __construct(
        Name $name,
        VHostName $vhost,
        UserName $user,
        int $number,
        NodeName $node,
        State $state,
        Messages $messages,
        Count $consumers,
        bool $confirm,
        bool $transactional,
        PointInTime $idleSince
    ) {
        $this->name = $name;
        $this->vhost = $vhost;
        $this->user = $user;
        $this->number = $number;
        $this->node = $node;
        $this->state = $state;
        $this->messages = $messages;
        $this->consumers = $consumers;
        $this->confirm = $confirm;
        $this->transactional = $transactional;
        $this->idleSince = $idleSince;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function vhost(): VHostName
    {
        return $this->vhost;
    }

    public function user(): UserName
    {
        return $this->user;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function node(): NodeName
    {
        return $this->node;
    }

    public function state(): State
    {
        return $this->state;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    public function consumers(): Count
    {
        return $this->consumers;
    }

    public function confirm(): bool
    {
        return $this->confirm;
    }

    public function transactional(): bool
    {
        return $this->transactional;
    }

    public function idleSince(): PointInTime
    {
        return $this->idleSince;
    }
}
