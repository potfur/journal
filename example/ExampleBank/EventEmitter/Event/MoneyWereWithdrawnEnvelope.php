<?php

namespace ExampleBank\EventEmitter\Event;

use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use ExampleBank\EventEmitter\DispatcherEnvelope;
use Journal\DomainEvent\DomainEvent;
use Symfony\Component\EventDispatcher\Event;

final class MoneyWereWithdrawnEnvelope extends Event implements DispatcherEnvelope
{
    /**
     * @var MoneyWereWithdrawnEvent
     */
    private $event;

    private function __construct(MoneyWereWithdrawnEvent $event)
    {
        $this->event = $event;
    }

    public static function getEventName()
    {
        return 'moneyWereWithdrawn';
    }

    public static function wrap(DomainEvent $event)
    {
        return new static($event);
    }

    public function getAccountNumber()
    {
        return $this->event->getAccountNumber();
    }

    public function getTransaction()
    {
        return $this->event->getTransaction();
    }

    public function getAmount()
    {
        return $this->event->getAmount();
    }

    public function getTimestamp()
    {
        return $this->event->getRecordedOn();
    }
}
