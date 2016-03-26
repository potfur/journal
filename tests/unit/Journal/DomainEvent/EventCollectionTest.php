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

class EventCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAppendTroughConstructor()
    {
        $collection = new EventCollection([$this->getMock(DomainEvent::class)]);

        $this->assertEquals(1, $collection->count());
    }

    public function testCountAppended()
    {
        $collection = new EventCollection();
        $collection->append($this->getMock(DomainEvent::class));

        $this->assertEquals(1, $collection->count());
    }

    public function testIterateOverAppended()
    {
        $event = $this->getMock(DomainEvent::class);

        $collection = new EventCollection();;
        $collection->append($event);

        foreach ($collection as $i => $element) {
            $this->assertEquals(0, $i);
            $this->assertSame($event, $element);
        }
    }
}
