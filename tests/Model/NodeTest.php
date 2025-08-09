<?php
declare(strict_types = 1);

namespace Tests\Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Node,
    Node\Name,
    Node\Type
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testInterface()
    {
        $node = Node::of(
            $name = Name::of('rabbit@foo'),
            $type = Type::disc,
            true,
        );

        $this->assertSame($name, $node->name());
        $this->assertSame($type, $node->type());
        $this->assertTrue($node->running());
    }
}
