<?php

namespace spec\ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyTransferRequestedEventSpec extends ObjectBehavior
{
    function let()
    {
        $accountNumber = AccountNumber::fromString('123ABC');
        $transfer = new Transfer(
            Transaction::fromString('000'),
            AccountNumber::fromString('456DEF'),
            100
        );

        $this->beConstructedWith(
            $accountNumber,
            $transfer
        );
    }

    function it_has_source_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_target_account_number()
    {
        $this->getAccountNumber()->shouldReturn('456DEF');
    }

    function it_has_withdrawn_amount()
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_has_a_timestamp_when_it_was_recorded()
    {
        $this->getRecordedOn()->shouldBeFloat();
    }
}
