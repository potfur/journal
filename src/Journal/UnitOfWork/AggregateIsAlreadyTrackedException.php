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

use Journal\Contract\Contract;
use Journal\EventStore\EventStreamIdentifier;

/**
 * Exception thrown when aggregate is already being tracked
 *
 * @package Journal\UnitOfWork
 */
class AggregateIsAlreadyTrackedException extends UnitOfWorkException
{
    /**
     * Create exception for specific contract and stream identifier
     *
     * @param Contract              $contract
     * @param EventStreamIdentifier $identifier
     *
     * @return static
     */
    public static function forContract(Contract $contract, EventStreamIdentifier $identifier)
    {
        return new static(
            sprintf(
                'Aggregate %s:%s is already tracked',
                $contract,
                $identifier
            )
        );
    }
}
