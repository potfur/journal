<?php

namespace ExampleBank\EventEmitter\Event;

use ExampleBank\DomainEvent\AccountOpenedEvent;
use ExampleBank\EventEmitter\DispatcherEnvelope;
use Journal\DomainEvent\DomainEvent;
use Symfony\Component\EventDispatcher\Event;

final class AccountOpenedEnvelope extends Event implements DispatcherEnvelope
{
    /**
     * @var AccountOpenedEvent
     */
    private $event;

    private function __construct(AccountOpenedEvent $event)
    {
        $this->event = $event;
    }

    public static function getEventName()
    {
        return 'accountOpened';
    }

    public static function wrap(DomainEvent $event)
    {
        return new static($event);
    }

    public function getAccountNumber()
    {
        return $this->event->getAccountNumber();
    }

    public function getTimestamp()
    {
        return $this->event->getRecordedOn();
    }
}
