<?php

namespace ExampleBank\EventEmitter;

use ExampleBank\DomainEvent\AccountOpenedEvent;
use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use ExampleBank\EventEmitter\Event\AccountOpenedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyTransferRequestedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereDepositedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereWithdrawnEnvelope;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;
use Journal\EventEmitter\EventEmitter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DispatcherEmitter implements EventEmitter
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var DispatcherEnvelope[]
     */
    private $envelopeMap = [
        AccountOpenedEvent::class => AccountOpenedEnvelope::class,
        MoneyWereDepositedEvent::class => MoneyWereDepositedEnvelope::class,
        MoneyWereWithdrawnEvent::class => MoneyWereWithdrawnEnvelope::class,
        MoneyTransferRequestedEvent::class => MoneyTransferRequestedEnvelope::class,
    ];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function emit(DomainEvents $events)
    {
        foreach ($events as $event) {
            $envelope = $this->findEnvelope($event);

            $this->dispatcher->dispatch(
                $envelope::getEventName(),
                $envelope::wrap($event)
            );
        }
    }

    private function findEnvelope(DomainEvent $event)
    {
        $eventClass = get_class($event);

        if (!array_key_exists($eventClass, $this->envelopeMap)) {
            throw new \InvalidArgumentException('Can\'t find envelope for unknown domain event ' . $eventClass);
        }

        return $this->envelopeMap[$eventClass];
    }
}
