<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\DomainEvent;

interface DomainEvent
{
    /**
     * Return event identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Return time when event was recorded
     *
     * @return float
     */
    public function getRecordedOn();

    /**
     * Return event version
     *
     * @return int
     */
    public function getVersion();
}
