<?php

namespace spec\ExampleBank\Domain;

use ExampleBank\Domain\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WithdrawSpec extends ObjectBehavior
{
    function it_has_amount()
    {
        $this->beConstructedWith(
            Transaction::fromString('000'),
            100
        );
        $this->getAmount()->shouldReturn(100);
    }

    function it_can_not_be_created_with_amount_smaller_than_1()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during(
            '__construct',
            [
                Transaction::fromString('000'),
                -100
            ]
        );
    }
}
