<?php

namespace spec\ExampleBank\EventEmitter\Event;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\DomainEvent\AccountOpenedEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountOpenedEnvelopeSpec extends ObjectBehavior
{
    function let()
    {
        $event = new AccountOpenedEvent(
            AccountNumber::fromString('123ABC')
        );

        $this->beConstructedThrough('wrap', [$event]);
    }

    function it_has_event_name()
    {
        $this::getEventName()->shouldReturn('accountOpened');
    }

    function it_has_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_timestamp()
    {
        $this->getTimestamp()->shouldBeDouble();
    }
}
