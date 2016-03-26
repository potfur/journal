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

use Journal\DomainEvent\DomainEvent;
use Journal\Contract\Contract;
use Journal\DomainEvent\EventCollection;
use Journal\EventStore\EventStreamIdentifier;

class InMemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemory
     */
    private $persistence;

    public function setUp()
    {
        $this->persistence = new InMemory();
    }

    public function testHasStream()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');

        $this->assertFalse($this->persistence->has($contract, $identifier));
    }

    public function testCommitStreamEnvelopes()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');
        $event = $this->getMock(DomainEvent::class);

        $this->persistence->commit($contract, $identifier, 0, new EventCollection([$event]));

        $this->assertTrue($this->persistence->has($contract, $identifier));
    }

    public function testCommitThrowsExceptionWhenExpectedRevisionDoesNotMatch()
    {
        $this->expectException(OptimisticConcurrencyFailedException::class);
        $this->expectExceptionMessage('Expected stream revision 123, got 0');

        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');
        $event = $this->getMock(DomainEvent::class);

        $this->persistence->commit($contract, $identifier, 123, new EventCollection([$event]));;
    }

    public function testFetchEmptyStreamEnvelopes()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');

        $result = $this->persistence->fetch($contract, $identifier);

        $this->assertEquals([], $result);
    }

    public function testFetchExistingStreamEnvelopes()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');
        $event = $this->getMock(DomainEvent::class);

        $this->persistence->commit($contract, $identifier, 0, new EventCollection([$event]));
        $result = $this->persistence->fetch($contract, $identifier);

        $this->assertEquals([$event], $result);
    }
}
