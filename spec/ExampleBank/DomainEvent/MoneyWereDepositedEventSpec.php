<?php

namespace spec\ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyWereDepositedEventSpec extends ObjectBehavior
{
    function let()
    {
        $accountNumber = AccountNumber::fromString('123ABC');
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $this->beConstructedWith($accountNumber, $deposit);
    }

    function it_has_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_deposited_amount()
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_has_a_timestamp_when_it_was_recorded()
    {
        $this->getRecordedOn()->shouldBeFloat();
    }
}
