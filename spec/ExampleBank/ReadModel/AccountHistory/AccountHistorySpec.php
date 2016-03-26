<?php

namespace spec\ExampleBank\ReadModel\AccountHistory;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\ReadModel\AccountHistory\Entry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountHistorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'), 0);
    }

    function it_is_created_for_specific_account()
    {
        $this->getAccountNumber()->__toString()->shouldReturn('123ABC');
    }

    function it_is_created_with_opening_entry()
    {
        $this->getHistory()[0]->getDescription()->shouldReturn('Open');
    }

    function it_appends_deposit_entry_when_deposited()
    {
        $this->deposit(0, 10);

        $entry = $this->getHistory()[1];

        $entry->getDescription()->shouldReturn('Deposit');
        $entry->getAmount()->shouldReturn(10);
        $entry->getDate()->getTimestamp()->shouldReturn(0);
    }

    function it_appends_withdraw_entry_when_withdrawn()
    {
        $this->withdraw(0, 10);

        $entry = $this->getHistory()[1];

        $entry->getDescription()->shouldReturn('Withdraw');
        $entry->getAmount()->shouldReturn(10);
        $entry->getDate()->getTimestamp()->shouldReturn(0);
    }
}
