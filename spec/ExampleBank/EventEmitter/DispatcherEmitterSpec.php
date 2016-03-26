<?php

namespace spec\ExampleBank\EventEmitter;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;
use ExampleBank\Domain\Withdraw;
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
use Journal\DomainEvent\EventCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DispatcherEmitterSpec extends ObjectBehavior
{
    function it_throws_exception_when_envelope_definition_is_missing(
        EventDispatcherInterface $dispatcher,
        DomainEvents $events,
        DomainEvent $event
    ) {
        $events->rewind()->willReturn(null);
        $events->valid()->willReturn(true);
        $events->current()->willReturn($event);

        $this->beConstructedWith($dispatcher);
        $this->shouldThrow(\InvalidArgumentException::class)->during('emit', [$events]);
    }

    function it_emits_account_opened_event(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            AccountOpenedEnvelope::getEventName(),
            Argument::type(AccountOpenedEnvelope::class)
        )->shouldBeCalled();

        $events = new EventCollection(
            [new AccountOpenedEvent(AccountNumber::fromString('123ABC'))]
        );

        $this->beConstructedWith($dispatcher);
        $this->emit($events);
    }

    function it_emits_money_were_deposited_event(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            MoneyWereDepositedEnvelope::getEventName(),
            Argument::type(MoneyWereDepositedEnvelope::class)
        )->shouldBeCalled();

        $events = new EventCollection(
            [
                new MoneyWereDepositedEvent(
                    AccountNumber::fromString('123ABC'),
                    new Deposit(Transaction::fromString('000'), 100)
                )
            ]
        );

        $this->beConstructedWith($dispatcher);
        $this->emit($events);
    }

    function it_emits_money_were_withdrawn_event(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            MoneyWereWithdrawnEnvelope::getEventName(),
            Argument::type(MoneyWereWithdrawnEnvelope::class)
        )->shouldBeCalled();

        $events = new EventCollection(
            [
                new MoneyWereWithdrawnEvent(
                    AccountNumber::fromString('123ABC'),
                    new Withdraw(Transaction::fromString('000'), 100)
                )
            ]
        );

        $this->beConstructedWith($dispatcher);
        $this->emit($events);
    }

    function it_emits_money_transfer_requested(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            MoneyTransferRequestedEnvelope::getEventName(),
            Argument::type(MoneyTransferRequestedEnvelope::class)
        )->shouldBeCalled();

        $events = new EventCollection(
            [
                new MoneyTransferRequestedEvent(
                    AccountNumber::fromString('123ABC'),
                    new Transfer(
                        Transaction::fromString('000'),
                        AccountNumber::fromString('456DEF'),
                        100
                    )
                )
            ]
        );

        $this->beConstructedWith($dispatcher);
        $this->emit($events);
    }
}
