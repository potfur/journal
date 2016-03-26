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

final class EventStream
{
    /**
     * @var Contract
     */
    private $aggregateContract;

    /**
     * @var EventStreamIdentifier
     */
    private $aggregateIdentifier;

    /**
     * @var DomainEvent[]
     */
    private $committed = [];

    /**
     * @var DomainEvent[]
     */
    private $pending = [];

    /**
     * @var Persistence
     */
    private $persistence;

    private function __construct(Persistence $persistence, Contract $contract, EventStreamIdentifier $identifier)
    {
        $this->persistence = $persistence;
        $this->aggregateContract = $contract;
        $this->aggregateIdentifier = $identifier;
    }

    /**
     * Create new empty stream
     *
     * @param Persistence           $persistence
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return EventStream
     */
    public static function create(Persistence $persistence, Contract $contract, EventStreamIdentifier $identifier)
    {
        return new EventStream($persistence, $contract, $identifier);
    }

    /**
     * Open stream
     *
     * @param Persistence           $persistence
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return EventStream
     */
    public static function open(Persistence $persistence, Contract $contract, EventStreamIdentifier $identifier)
    {
        $eventStream = new EventStream($persistence, $contract, $identifier);
        $eventStream->committed = $persistence->fetch($contract, $identifier);

        return $eventStream;
    }

    /**
     * Add event to the end of stream
     *
     * @param DomainEvent $event
     */
    public function append(DomainEvent $event)
    {
        $this->pending[] = $event;
    }

    /**
     * Persist streams envelopes
     */
    public function commit()
    {
        if (empty($this->pending)) {
            return;
        }

        $this->persistence->commit(
            $this->aggregateContract,
            $this->aggregateIdentifier,
            count($this->committed),
            new EventCollection($this->pending)
        );

        $this->committed = array_merge($this->committed, $this->pending);
        $this->pending = [];
    }

    /**
     * Return all envelopes
     *
     * @return DomainEvent[]
     */
    public function all()
    {
        return array_merge($this->committed, $this->pending);
    }
}
