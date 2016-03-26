<?php

namespace spec\ExampleBank\EventEmitter\Event;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Withdraw;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyWereWithdrawnEnvelopeSpec extends ObjectBehavior
{
    function let()
    {
        $event = new MoneyWereWithdrawnEvent(
            AccountNumber::fromString('123ABC'),
            new Withdraw(
                Transaction::fromString('000'),
                100
            )
        );

        $this->beConstructedThrough('wrap', [$event]);
    }

    function it_has_event_name()
    {
        $this::getEventName()->shouldReturn('moneyWereWithdrawn');
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
