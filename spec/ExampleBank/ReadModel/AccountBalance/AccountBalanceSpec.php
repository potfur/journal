<?php

namespace spec\ExampleBank\ReadModel\AccountBalance;

use ExampleBank\Domain\AccountNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountBalanceSpec extends ObjectBehavior
{
    function it_is_created_for_specific_account()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'));

        $this->getAccountNumber()->__toString()->shouldReturn('123ABC');
    }

    function it_is_created_with_balance_equal_zero()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'));

        $this->getBalance()->shouldReturn(0);
    }

    function it_can_increase_balance_by_set_amount()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'));

        $this->increaseBy(100);
        $this->getBalance()->shouldReturn(100);
    }

    function it_can_decreased_balance_by_set_amount()
    {
        $this->beConstructedWith(AccountNumber::fromString('123ABC'));

        $this->decreaseBy(100);
        $this->getBalance()->shouldReturn(-100);
    }
}
