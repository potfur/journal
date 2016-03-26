<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\EventStore;

use Journal\Contract\Contract;
use Journal\Persistence\Persistence;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Persistence|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistence;

    /**
     * @var EventStore
     */
    private $eventStore;

    public function setUp()
    {
        $this->persistence = $this->getMock(Persistence::class);
        $this->eventStore = new EventStore($this->persistence);
    }

    public function testOpenNewStream()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');

        $stream = $this->eventStore->createStream($contract, $identifier);

        $this->assertInstanceOf(EventStream::class, $stream);
        $this->assertEquals([], $stream->all());
    }

    public function testOpenExistingStream()
    {
        $contract = Contract::fromClass(\stdClass::class);
        $identifier = EventStreamIdentifier::fromString('Foo');

        $this->persistence->expects($this->once())->method('fetch')->with($contract, $identifier)->willReturn([]);

        $stream = $this->eventStore->openStream($contract, $identifier);

        $this->assertInstanceOf(EventStream::class, $stream);
        $this->assertEquals([], $stream->all());
    }
}
