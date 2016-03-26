<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\EventEmitter;

use Journal\DomainEvent\DomainEvents;

interface EventEmitter
{
    /**
     * @param DomainEvents $events
     *
     * @return void
     */
    public function emit(DomainEvents $events);
}
