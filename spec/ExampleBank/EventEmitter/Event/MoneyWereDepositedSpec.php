<?php

namespace spec\ExampleBank\EventEmitter\Event;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyWereDepositedEnvelopeSpec extends ObjectBehavior
{
    function let()
    {
        $event = new MoneyWereDepositedEvent(
            AccountNumber::fromString('123ABC'),
            new Deposit(
                Transaction::fromString('000'),
                100
            )
        );

        $this->beConstructedThrough('wrap', [$event]);
    }

    function it_has_event_name()
    {
        $this::getEventName()->shouldReturn('moneyWereDeposited');
    }

    function it_has_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_transaction_number()
    {
        $this->getTransaction()->shouldReturn('000');
    }

    function it_has_amount()
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_has_timestamp()
    {
        $this->getTimestamp()->shouldBeDouble();
    }
}
