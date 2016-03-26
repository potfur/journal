<?php

namespace spec\ExampleBank\ReadModel\AccountExport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountExportSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('123ABC', 0);
    }

    function it_has_account_number()
    {
        $this->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_has_empty_history_on_beginning()
    {
        $this->getHistory()->shouldReturn(
            [
                [0, '=', 0, 0],
            ]
        );
    }

    function it_increases_balance()
    {
        $this->increaseBy(10, 0);
        $this->getHistory()->shouldReturn(
            [
                [0, '=', 0, 0],
                [0, '+', 10, 10]
            ]
        );
    }

    function it_decreases_balance()
    {
        $this->decreaseBy(10, 0);
        $this->getHistory()->shouldReturn(
            [
                [0, '=', 0, 0],
                [0, '-', 10, -10]
            ]
        );
    }
}
