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
use Journal\DomainEvent\DomainEvents;
use Journal\EventStore\EventStreamIdentifier;
use Journal\Persistence\Persistence;

final class InMemory implements Persistence
{
    private $storage = [];

    private function getKey(Contract $contract, EventStreamIdentifier $identifier)
    {
        return (string) $contract . '\\' . (string) $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function has(Contract $contract, EventStreamIdentifier $identifier)
    {
        return array_key_exists($this->getKey($contract, $identifier), $this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Contract $contract, EventStreamIdentifier $identifier)
    {
        $key = $this->getKey($contract, $identifier);
        if (!array_key_exists($key, $this->storage)) {
            return [];
        }

        return $this->storage[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function commit(
        Contract $contract,
        EventStreamIdentifier $identifier,
        $expectedRevision,
        DomainEvents $events
    ) {
        $contractIdentifier = $this->getKey($contract, $identifier);

        if (!array_key_exists($contractIdentifier, $this->storage)) {
            $this->storage[$contractIdentifier] = [];
        }

        if (count($this->storage[$contractIdentifier]) !== $expectedRevision) {
            throw new OptimisticConcurrencyFailedException(
                sprintf(
                    'Expected stream revision %s, got %s',
                    $expectedRevision,
                    count($this->storage[$contractIdentifier])
                )
            );
        }

        foreach ($events as $envelope) {
            $this->storage[$contractIdentifier][] = $envelope;
        }
    }
}
