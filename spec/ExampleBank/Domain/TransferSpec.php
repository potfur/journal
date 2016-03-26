<?php

namespace spec\ExampleBank\Domain;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            Transaction::fromString('000'),
            AccountNumber::fromString('123ABC'),
            100
        );
    }

    function it_has_target_account()
    {
        $this->getTargetAccountNumber()->__toString()->shouldReturn('123ABC');
    }

    function it_has_amount()
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_can_not_be_created_with_amount_smaller_than_1()
    {
        $this->shouldThrow(\InvalidArgumentException::class)
            ->during(
                '__construct',
                [
                    Transaction::fromString('000'),
                    AccountNumber::fromString('123ABC'),
                    -100
                ]
            );
    }
}
