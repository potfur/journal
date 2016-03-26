<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal;

use Journal\AggregateRoot\AggregateRoot;
use Journal\Contract\Contract;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;
use Journal\EventEmitter\EventEmitter;
use Journal\EventStore\EventIdentifier;
use Journal\EventStore\EventStore;
use Journal\EventStore\EventStreamIdentifier;
use Journal\Persistence\Persistence;
use Journal\UnitOfWork\UnitOfWork;

class AggregateRootDouble implements AggregateRoot
{
    public $events;

    public function getRecordedEvents()
    {
    }

    public function clearRecordedEvents()
    {
    }

    public static function reconstituteFrom(DomainEvents $events)
    {
        $instance = new static;
        $instance->events = $events;

        return $instance;
    }
}

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Persistence|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistence;

    /**
     * @var EventEmitter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventEmitter;

    /**
     * @var UnitOfWork
     */
    private $uow;

    public function setUp()
    {
        $this->persistence = $this->getMock(Persistence::class);
        $this->eventEmitter = $this->getMock(EventEmitter::class);

        $this->uow = new UnitOfWork(
            new EventStore($this->persistence),
            $this->eventEmitter
        );
    }

    public function testEventsWillBePersisted()
    {
        $contract = Contract::fromClass(DomainEvent::class);
        $identifier = EventIdentifier::fromString('1');
        $eventStreamId = EventStreamIdentifier::fromString('test');

        $event = $this->getMock(DomainEvent::class);
        $event->expects($this->any())->method('getIdentifier')->willReturn($identifier);

        $aggregateRoot = $this->getMock(AggregateRoot::class);
        $aggregateRoot->expects($this->any())->method('getRecordedEvents')->willReturn([$event]);

        $this->persistence->expects($this->any())->method('fetch')->willReturn([]);
        $this->persistence->expects($this->once())->method('commit')->with(
            $contract,
            $eventStreamId,
            $this->isType('integer'),
            $this->callback(
                function ($subject) use ($event) {
                    return iterator_to_array($subject)[0] == $event;
                }
            )
        );

        $this->uow->track($contract, $eventStreamId, $aggregateRoot);
        $this->uow->commit();
    }

    public function testEventsWillBeEmitted()
    {
        $contract = Contract::fromClass(DomainEvent::class);
        $identifier = EventIdentifier::fromString('1');
        $eventStreamId = EventStreamIdentifier::fromString('test');

        $event = $this->getMock(DomainEvent::class);
        $event->expects($this->any())->method('getIdentifier')->willReturn($identifier);

        $aggregateRoot = $this->getMock(AggregateRoot::class);
        $aggregateRoot->expects($this->any())->method('getRecordedEvents')->willReturn([$event]);

        $this->persistence->expects($this->any())->method('fetch')->willReturn([]);

        $this->eventEmitter->expects($this->once())->method('emit')->with(
            $this->callback(
                function ($subject) {
                    return $subject instanceof DomainEvents && count($subject) == 1;
                }
            )
        );

        $this->uow->track($contract, $eventStreamId, $aggregateRoot);
        $this->uow->commit();
    }

    public function testRootWillBeReconstitutedFromStream()
    {
        $contract = Contract::fromClass(AggregateRootDouble::class);
        $eventStreamId = EventStreamIdentifier::fromString('test');

        $event = $this->getMock(DomainEvent::class);

        $this->persistence->expects($this->any())->method('fetch')->willReturn([$event]);

        $aggregate = $this->uow->get($contract, $eventStreamId);

        $this->assertEquals([$event], iterator_to_array($aggregate->events));
    }
}
