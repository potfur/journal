<?php

namespace spec\ExampleBank\EventEmitter\Event;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;
use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyTransferRequestedEnvelopeSpec extends ObjectBehavior
{
    function let()
    {
        $event = new MoneyTransferRequestedEvent(
            AccountNumber::fromString('123ABC'),
            new Transfer(
                Transaction::fromString('000'),
                AccountNumber::fromString('456DEF'),
                100
            )
        );

        $this->beConstructedThrough('wrap', [$event]);
    }

    function it_has_event_name()
    {
        $this::getEventName()->shouldReturn('moneyTransferRequested');
    }

    function it_has_source_account_number()
    {
        $this->getSourceAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_target_account_number()
    {
        $this->getTargetAccountNumber()->shouldReturn('456DEF');
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
