<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Connection\Name,
    Connection\Peer,
    Connection\Timeout,
    Connection\Protocol,
    Connection\AuthenticationMechanism,
    Connection\Type,
    Node\Name as NodeName,
    VHost\Name as VHostName,
    User\Name as UserName
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Url\Authority\{
    Host,
    Port,
};

final class Connection
{
    private Name $name;
    private PointInTime $connectedAt;
    private Timeout $timeout;
    private VHostName $vhost;
    private UserName $user;
    private Protocol $protocol;
    private AuthenticationMechanism $authenticationMechanism;
    private bool $ssl;
    private Peer $peer;
    private Host $host;
    private Port $port;
    private NodeName $node;
    private Type $type;
    private State $state;

    public function __construct(
        Name $name,
        PointInTime $connectedAt,
        Timeout $timeout,
        VHostName $vhost,
        UserName $user,
        Protocol $protocol,
        AuthenticationMechanism $authenticationMechanism,
        bool $ssl,
        Peer $peer,
        Host $host,
        Port $port,
        NodeName $node,
        Type $type,
        State $state
    ) {
        $this->connectedAt = $connectedAt;
        $this->timeout = $timeout;
        $this->vhost = $vhost;
        $this->user = $user;
        $this->protocol = $protocol;
        $this->authenticationMechanism = $authenticationMechanism;
        $this->ssl = $ssl;
        $this->peer = $peer;
        $this->host = $host;
        $this->port = $port;
        $this->name = $name;
        $this->node = $node;
        $this->type = $type;
        $this->state = $state;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function connectedAt(): PointInTime
    {
        return $this->connectedAt;
    }

    public function timeout(): Timeout
    {
        return $this->timeout;
    }

    public function vhost(): VHostName
    {
        return $this->vhost;
    }

    public function user(): UserName
    {
        return $this->user;
    }

    public function protocol(): Protocol
    {
        return $this->protocol;
    }

    public function authenticationMechanism(): AuthenticationMechanism
    {
        return $this->authenticationMechanism;
    }

    public function ssl(): bool
    {
        return $this->ssl;
    }

    public function peer(): Peer
    {
        return $this->peer;
    }

    public function host(): Host
    {
        return $this->host;
    }

    public function port(): Port
    {
        return $this->port;
    }

    public function node(): NodeName
    {
        return $this->node;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function state(): State
    {
        return $this->state;
    }
}
