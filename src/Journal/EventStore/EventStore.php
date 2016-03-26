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
use Journal\Persistence\Persistence;

final class EventStore
{
    /**
     * @var Persistence
     */
    private $persistence;

    /**
     * EventStore constructor.
     *
     * @param Persistence $persistence
     */
    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    /**
     * Create new (empty) stream
     *
     * @param Contract              $streamContract
     * @param EventStreamIdentifier $streamIdentifier
     *
     * @return EventStream
     */
    public function createStream(Contract $streamContract, EventStreamIdentifier $streamIdentifier)
    {
        return EventStream::create($this->persistence, $streamContract, $streamIdentifier);
    }

    /**
     * Open stream
     *
     * @param Contract              $streamContract
     * @param EventStreamIdentifier $streamIdentifier
     *
     * @return EventStream
     */
    public function openStream(Contract $streamContract, EventStreamIdentifier $streamIdentifier)
    {
        return EventStream::open($this->persistence, $streamContract, $streamIdentifier);
    }
}
