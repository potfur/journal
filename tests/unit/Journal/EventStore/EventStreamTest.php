<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\EventStore;

use Journal\Contract\Contract;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\EventCollection;
use Journal\Persistence\Persistence;

class EventStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Persistence|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistence;

    public function setUp()
    {
        $this->persistence = $this->getMock(Persistence::class);
    }

    public function testCreateStream()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $contract = Contract::fromClass(\stdClass::class);

        $stream = EventStream::create($this->persistence, $contract, $identifier);

        $this->assertInstanceOf(EventStream::class, $stream);
        $this->assertCount(0, $stream->all());
    }

    public function testOpenStream()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $contract = Contract::fromClass(\stdClass::class);

        $this->persistence
            ->expects($this->once())
            ->method('fetch')->with($contract, $identifier)
            ->willReturn([$this->getMock(DomainEvent::class)]);

        $stream = EventStream::open($this->persistence, $contract, $identifier);

        $this->assertInstanceOf(EventStream::class, $stream);
        $this->assertCount(1, $stream->all());
    }

    public function testAppendEvent()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $contract = Contract::fromClass(\stdClass::class);

        $stream = EventStream::create($this->persistence, $contract, $identifier);
        $stream->append($this->getMock(DomainEvent::class));

        $this->assertCount(1, $stream->all());
    }

    public function testCommitPendingEvents()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $contract = Contract::fromClass(\stdClass::class);
        $event = $this->getMock(DomainEvent::class);

        $this->persistence->expects($this->once())->method('commit')->with($contract, $identifier, 0, new EventCollection([$event]));

        $stream = EventStream::create($this->persistence, $contract, $identifier);
        $stream->append($event);

        $stream->commit();
    }

    public function testCommitEmpty()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $contract = Contract::fromClass(\stdClass::class);

        $this->persistence->expects($this->never())->method('commit');

        $stream = EventStream::create($this->persistence, $contract, $identifier);
        $stream->commit();
    }
}
