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
use Journal\Contract\Contract;
use Journal\Identifier\Identifier;

class AggregateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var Identifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $identifier;

    /**
     * @var AggregateRoot|\PHPUnit_Framework_MockObject_MockObject
     */
    private $root;

    /**
     * @var Aggregate
     */
    private $aggregate;

    public function setUp()
    {
        $this->contract = Contract::fromClass(static::class);
        $this->identifier = $this->getMock(Identifier::class);
        $this->root = $this->getMock(AggregateRoot::class);

        $this->aggregate = new Aggregate($this->contract, $this->identifier, $this->root);
    }

    public function testGetContract()
    {
        $this->assertSame($this->contract, $this->aggregate->getContract());
    }

    public function testGetIdentifier()
    {
        $this->assertSame($this->identifier, $this->aggregate->getIdentifier());
    }

    public function testGetAggregateRoot()
    {
        $this->assertSame($this->root, $this->aggregate->getAggregateRoot());
    }

    public function testGetRootsRecordedEvents()
    {
        $this->root->expects($this->once())->method('getRecordedEvents')->willReturn([]);

        $this->assertEquals([], $this->aggregate->getChanges());
    }

    public function testClearRootsRecordedEvents()
    {
        $this->root->expects($this->once())->method('clearRecordedEvents');

        $this->aggregate->clearChanges();
    }

    public function testIsIdentifiedByContractAndIdentifier()
    {
        $this->identifier->expects($this->any())->method('equals')->willReturn(true);

        $this->assertTrue($this->aggregate->isIdentifiedBy($this->contract, $this->identifier));
    }

    public function testIsNotIdentifiedBecauseOfDifferentContract()
    {
        $this->identifier->expects($this->any())->method('equals')->willReturn(true);

        $this->assertFalse($this->aggregate->isIdentifiedBy(Contract::fromString('foo'), $this->identifier));
    }

    public function testIsNotIdentifiedBecauseOfDifferentIdentifier()
    {
        $this->identifier->expects($this->any())->method('equals')->willReturn(false);

        $this->assertFalse($this->aggregate->isIdentifiedBy($this->contract, $this->identifier));
    }
}
