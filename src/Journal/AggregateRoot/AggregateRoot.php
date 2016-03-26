<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\AggregateRoot;

use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;

interface AggregateRoot
{
    /**
     * Return recorded events
     *
     * @return DomainEvent[]
     */
    public function getRecordedEvents();

    /**
     * Remove recorded events
     *
     * @return void
     */
    public function clearRecordedEvents();

    /**
     * Reconstitute state from events
     *
     * @param DomainEvents $events
     *
     * @return static
     */
    public static function reconstituteFrom(DomainEvents $events);
}
