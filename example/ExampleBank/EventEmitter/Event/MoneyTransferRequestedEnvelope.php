<?php

namespace ExampleBank\EventEmitter\Event;

use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use ExampleBank\EventEmitter\DispatcherEnvelope;
use Journal\DomainEvent\DomainEvent;
use Symfony\Component\EventDispatcher\Event;

final class MoneyTransferRequestedEnvelope extends Event implements DispatcherEnvelope
{
    /**
     * @var MoneyTransferRequestedEvent
     */
    private $event;

    private function __construct(MoneyTransferRequestedEvent $event)
    {
        $this->event = $event;
    }

    public static function getEventName()
    {
        return 'moneyTransferRequested';
    }

    public static function wrap(DomainEvent $event)
    {
        return new static($event);
    }

    public function getSourceAccountNumber()
    {
        return $this->event->getSourceAccountNumber();
    }

    public function getTargetAccountNumber()
    {
        return $this->event->getTargetAccountNumber();
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
