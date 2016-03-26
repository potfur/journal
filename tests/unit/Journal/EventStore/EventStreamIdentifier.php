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

class EventStreamIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromString()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $this->assertEquals('Foo', (string) $identifier);
    }

    public function testIsEqual()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $this->assertTrue($identifier->equals(EventStreamIdentifier::fromString('Foo')));
    }

    public function testIsNOtEqual()
    {
        $identifier = EventStreamIdentifier::fromString('Foo');
        $this->assertTrue($identifier->equals(EventStreamIdentifier::fromString('Bar')));
    }
}
