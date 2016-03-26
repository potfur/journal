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

/**
 * Exception thrown when aggregate is not an instance of AggregateRoot
 *
 * @package Journal\UnitOfWork
 */
class AggregateIsInvalidInstanceException extends UnitOfWorkException
{
    /**
     * Create exception for specific aggregate class
     *
     * @param string $aggregateClassName
     *
     * @return static
     */
    public static function forClass($aggregateClassName)
    {
        return new static(
            sprintf(
                'Aggregate root class %s is not instance of %s',
                $aggregateClassName,
                AggregateRoot::class
            )
        );
    }
}
