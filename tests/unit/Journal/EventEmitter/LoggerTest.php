<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\Emitter;

use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\EventCollection;
use Journal\EventEmitter\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testEmit()
    {
        $event = $this->getMock(DomainEvent::class);

        $emitter = new Logger();
        $emitter->emit(new EventCollection([$event]));

        $this->assertEquals([$event], $emitter->all());
    }
}
