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

final class EventCollection implements DomainEvents
{
    private $events = [];

    /**
     * EventCollection constructor.
     *
     * @param DomainEvent[] $events
     */
    public function __construct(array $events = [])
    {
        foreach ($events as $event) {
            $this->append($event);
        }
    }

    /**
     * Append event into collection
     *
     * @param DomainEvent $event
     */
    public function append(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * Count events in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * Return domain event
     *
     * @return DomainEvent
     */
    public function current()
    {
        return current($this->events);
    }

    /**
     * Return the key of current event
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->events);
    }

    /**
     * Move forward to next event
     */
    public function next()
    {
        next($this->events);
    }

    /**
     * Rewind the Iterator to the first event
     */
    public function rewind()
    {
        reset($this->events);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->events) !== null;
    }
}
