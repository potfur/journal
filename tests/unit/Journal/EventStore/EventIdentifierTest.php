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

class EventIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromString()
    {
        $identifier = EventIdentifier::fromString('Foo');
        $this->assertEquals('Foo', (string) $identifier);
    }

    public function testIsEqual()
    {
        $identifier = EventIdentifier::fromString('Foo');
        $this->assertTrue($identifier->equals(EventIdentifier::fromString('Foo')));
    }

    public function testIsNotEqual()
    {
        $identifier = EventIdentifier::fromString('Foo');
        $this->assertFalse($identifier->equals(EventIdentifier::fromString('Bar')));
    }
}
