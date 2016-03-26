<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\Persistence;

use Journal\Contract\Contract;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;
use Journal\EventStore\EventStreamIdentifier;

interface Persistence
{
    /**
     * Retrieve stream identified by contract and identifier
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return DomainEvent[]
     */
    public function fetch(Contract $contract, EventStreamIdentifier $identifier);

    /**
     * Persist stream envelopes
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     * @param int                   $expectedRevision number of commits to this point
     * @param DomainEvents          $events
     *
     * @return void
     */
    public function commit(
        Contract $contract,
        EventStreamIdentifier $identifier,
        $expectedRevision,
        DomainEvents $events
    );
}
