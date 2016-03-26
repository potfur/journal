<?php

namespace spec\ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountOpenedEventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'));
    }

    function it_has_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_a_timestamp_when_it_was_recorded()
    {
        $this->getRecordedOn()->shouldBeFloat();
    }
}
