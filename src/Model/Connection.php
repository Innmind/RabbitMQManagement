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
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Url\Authority\{
    HostInterface,
    PortInterface
};

final class Connection
{
    private $name;
    private $connectedAt;
    private $timeout;
    private $vhost;
    private $user;
    private $protocol;
    private $authenticationMechanism;
    private $ssl;
    private $peer;
    private $host;
    private $port;
    private $node;
    private $type;
    private $state;

    public function __construct(
        Name $name,
        PointInTimeInterface $connectedAt,
        Timeout $timeout,
        VHostName $vhost,
        UserName $user,
        Protocol $protocol,
        AuthenticationMechanism $authenticationMechanism,
        bool $ssl,
        Peer $peer,
        HostInterface $host,
        PortInterface $port,
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

    public function connectedAt(): PointInTimeInterface
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

    public function host(): HostInterface
    {
        return $this->host;
    }

    public function port(): PortInterface
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
