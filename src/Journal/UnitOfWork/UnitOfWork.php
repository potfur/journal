<?php

namespace Journal\UnitOfWork;

use Journal\EventEmitter\EventEmitter;
use Journal\EventStore\EventStreamIdentifier;
use Journal\AggregateRoot\AggregateRoot;
use Journal\DomainEvent\EventCollection;
use Journal\Contract\Contract;
use Journal\EventStore\EventStore;
use Journal\EventStore\EventStream;

final class UnitOfWork
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventEmitter
     */
    private $eventEmitter;

    /**
     * @var Aggregate[]
     */
    private $tracked = [];

    /**
     * UnitOfWork constructor.
     *
     * @param EventStore   $eventStore
     * @param EventEmitter $eventEmitter
     */
    public function __construct(EventStore $eventStore, EventEmitter $eventEmitter)
    {
        $this->eventStore = $eventStore;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Start tracking changes in aggregate root
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     * @param AggregateRoot         $aggregateRoot
     *
     * @throws UnitOfWorkException
     */
    public function track(Contract $contract, EventStreamIdentifier $identifier, AggregateRoot $aggregateRoot)
    {
        if ($this->findTracked($contract, $identifier)) {
            throw AggregateIsAlreadyTrackedException::forContract($contract, $identifier);
        }

        $this->tracked[] = new Aggregate($contract, $identifier, $aggregateRoot);
    }

    /**
     * Check if aggregate root is tracked
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return bool
     */
    public function isTracked(Contract $contract, EventStreamIdentifier $identifier)
    {
        return $this->findTracked($contract, $identifier) !== null;
    }

    private function findTracked(Contract $contract, EventStreamIdentifier $identifier)
    {
        foreach ($this->tracked as $tracked) {
            if ($tracked->isIdentifiedBy($contract, $identifier)) {
                return $tracked;
            }
        }

        return null;
    }

    /**
     * Return aggregate root identified by contract and identifier
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return AggregateRoot
     * @throws UnitOfWorkException
     */
    public function get(Contract $contract, EventStreamIdentifier $identifier)
    {
        if ($aggregate = $this->findTracked($contract, $identifier)) {
            return $aggregate->getAggregateRoot();
        }

        $aggregateRoot = $this->reconstitute(
            $contract->toClassName(),
            $this->eventStore->openStream($contract, $identifier)
        );

        $this->track($contract, $identifier, $aggregateRoot);

        return $aggregateRoot;
    }

    private function reconstitute($aggregateClassName, EventStream $stream)
    {
        if (!is_a($aggregateClassName, AggregateRoot::class, true)) {
            throw AggregateIsInvalidInstanceException::forClass($aggregateClassName);
        }

        /** @var $aggregateClassName AggregateRoot */
        return $aggregateClassName::reconstituteFrom(new EventCollection($stream->all()));
    }

    /**
     * Commit changes in tracked aggregates
     */
    public function commit()
    {
        foreach ($this->tracked as $aggregate) {
            $this->persistAggregate($aggregate);
        }

        $this->tracked = [];
    }

    private function persistAggregate(Aggregate $aggregate)
    {
        if (empty($aggregate->getChanges())) {
            return;
        }

        $events = new EventCollection($aggregate->getChanges());

        $stream = $this->eventStore->openStream(
            $aggregate->getContract(),
            EventStreamIdentifier::fromString($aggregate->getIdentifier())
        );

        foreach ($events as $event) {
            $stream->append($event);
        }

        $stream->commit();

        $this->eventEmitter->emit($events);

        $aggregate->clearChanges();
    }
}
