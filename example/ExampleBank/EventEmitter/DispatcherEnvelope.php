<?php

namespace ExampleBank\EventEmitter;

use Journal\DomainEvent\DomainEvent;

interface DispatcherEnvelope
{
    public static function getEventName();

    public static function wrap(DomainEvent $event);
}
