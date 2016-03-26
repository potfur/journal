<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\UnitOfWork;

use Journal\AggregateRoot\AggregateRoot;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;
use Journal\Contract\Contract;
use Journal\EventEmitter\EventEmitter;
use Journal\EventStore\EventIdentifier;
use Journal\EventStore\EventStore;
use Journal\EventStore\EventStreamIdentifier;
use Journal\Persistence\Persistence;

class FakeAggregateRoot implements AggregateRoot
{
    private $events = [];

    public function __construct(DomainEvents $events)
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    public function getRecordedEvents()
    {
        return $this->events;
    }

    public function clearRecordedEvents()
    {
        // NOOP
    }

    public static function reconstituteFrom(DomainEvents $events)
    {
        return new static($events);
    }
}

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Persistence|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistence;

    /**
     * @var EventStore
     */
    private $eventStore;

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
        $this->eventStore = new EventStore($this->persistence);
        $this->eventEmitter = $this->getMock(EventEmitter::class);

        $this->uow = new UnitOfWork($this->eventStore, $this->eventEmitter);
    }

    public function testTrack()
    {
        $contract = Contract::fromClass(static::class);
        $identifier = EventStreamIdentifier::fromString('foo');
        $root = $this->getMock(AggregateRoot::class);

        $this->uow->track($contract, $identifier, $root);
        $this->assertTrue($this->uow->isTracked($contract, $identifier));
    }

    public function testTrackAlreadyTracked()
    {
        $this->expectException(AggregateIsAlreadyTrackedException::class);
        $this->expectExceptionMessage('Aggregate journal.unit_of_work.unit_of_work_test:foo is already tracked');

        $contract = Contract::fromClass(static::class);
        $identifier = EventStreamIdentifier::fromString('foo');
        $root = $this->getMock(AggregateRoot::class);

        $this->uow->track($contract, $identifier, $root);
        $this->uow->track($contract, $identifier, $root);
    }

    public function testGetTracked()
    {
        $contract = Contract::fromClass(static::class);
        $identifier = EventStreamIdentifier::fromString('foo');
        $root = $this->getMock(AggregateRoot::class);

        $this->uow->track($contract, $identifier, $root);
        $this->assertSame($root, $this->uow->get($contract, $identifier));
    }

    public function testGetReconstituted()
    {
        $contract = Contract::fromClass(FakeAggregateRoot::class);
        $identifier = EventStreamIdentifier::fromString('foo');
        $event = $this->getMock(DomainEvent::class);

        $this->persistence->expects($this->once())->method('fetch')->with($contract, $identifier)->willReturn([$event]);

        $result = $this->uow->get($contract, $identifier);

        $this->assertInstanceOf(FakeAggregateRoot::class, $result);
        $this->assertEquals([$event], $result->getRecordedEvents());
    }

    public function testGetReconstitutedWithInvalidContractClass()
    {
        $this->expectException(AggregateIsInvalidInstanceException::class);
        $this->expectExceptionMessage('Aggregate root class StdClass is not instance of ' . AggregateRoot::class);

        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('foo');
        $event = $this->GetMock(DomainEvent::class);

        $this->persistence->expects($this->once())->method('fetch')->with($contract, $identifier)->willReturn([$event]);

        $this->uow->get($contract, $identifier);
    }

    public function testCommit()
    {
        $contract = Contract::fromClass(static::class);
        $identifier = EventStreamIdentifier::fromString('foo');

        $event = $this->getMock(DomainEvent::class);
        $event->expects($this->any())->method('getIdentifier')->willReturn(EventIdentifier::fromString('Foo'));

        $root = $this->getMock(AggregateRoot::class);
        $root->expects($this->any())->method('getRecordedEvents')->willReturn([$event]);

        $this->persistence->expects($this->any())->method('fetch')->willReturn([]);

        $this->persistence->expects($this->once())->method('commit')->with(
            $this->isInstanceOf(Contract::class),
            $this->isInstanceOf(EventStreamIdentifier::class),
            0,
            $this->callback(
                function ($subject) {
                    return $subject instanceof DomainEvents && count($subject) == 1;
                }
            )
        );

        $this->eventEmitter->expects($this->once())->method('emit')->with(
            $this->callback(
                function ($subject) {
                    return $subject instanceof DomainEvents && count($subject) == 1;
                }
            )
        );

        $this->uow->track($contract, $identifier, $root);
        $this->uow->commit();
    }

    public function testCommitStreamWithoutChanges()
    {
        $contract = Contract::fromClass(static::class);
        $identifier = EventStreamIdentifier::fromString('foo');

        $root = $this->getMock(AggregateRoot::class);
        $root->expects($this->any())->method('getRecordedEvents')->willReturn([]);

        $this->persistence->expects($this->any())->method('fetch')->willReturn([]);

        $this->persistence->expects($this->never())->method('commit');
        $this->eventEmitter->expects($this->never())->method('emit');

        $this->uow->track($contract, $identifier, $root);
        $this->uow->commit();
    }
}
