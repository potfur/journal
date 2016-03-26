<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\UnitOfWork;

use Journal\AggregateRoot\AggregateRoot;
use Journal\DomainEvent\DomainEvent;
use Journal\Contract\Contract;
use Journal\Identifier\Identifier;

final class Aggregate
{
    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var Identifier
     */
    private $identifier;

    /**
     * @var AggregateRoot
     */
    private $aggregateRoot;

    /**
     * Aggregate constructor.
     *
     * @param Contract      $contract
     * @param Identifier    $identifier
     * @param AggregateRoot $aggregateRoot
     */
    public function __construct(Contract $contract, Identifier $identifier, AggregateRoot $aggregateRoot)
    {

        $this->contract = $contract;
        $this->identifier = $identifier;
        $this->aggregateRoot = $aggregateRoot;
    }

    /**
     * Return aggregate root contract
     *
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * Return aggregate root identifier
     *
     * @return Identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Return aggregate root instance
     *
     * @return AggregateRoot
     */
    public function getAggregateRoot()
    {
        return $this->aggregateRoot;
    }

    /**
     * Return aggregate roots recorded events
     *
     * @return DomainEvent[]
     */
    public function getChanges()
    {
        return $this->aggregateRoot->getRecordedEvents();
    }

    /**
     * Clear aggregate roots recorded events
     */
    public function clearChanges()
    {
        $this->aggregateRoot->clearRecordedEvents();
    }

    /**
     * Check if aggregate is identified by contract and identifier
     *
     * @param Contract   $contract
     * @param Identifier $identifier
     *
     * @return bool
     */
    public function isIdentifiedBy(Contract $contract, Identifier $identifier)
    {
        return $this->contract->equals($contract) && $this->identifier->equals($identifier);
    }
}
