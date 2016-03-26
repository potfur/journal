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
use Journal\EventEmitter\EventEmitter;

class Logger implements EventEmitter
{
    private $logged = [];

    public function emit(DomainEvents $events)
    {
        foreach ($events as $event) {
            $this->logged[] = $event;
        }
    }

    public function all()
    {
        return $this->logged;
    }
}
