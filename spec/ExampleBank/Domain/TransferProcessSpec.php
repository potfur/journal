<?php

namespace spec\ExampleBank\Domain;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferProcessSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            Transaction::fromString('000'),
            AccountNumber::fromString('123ABC'),
            AccountNumber::fromString('456DEF'),
            100
        );
    }

    function it_has_transaction_identifier()
    {
        $this->getTransaction()->__toString()->shouldReturn('000');
    }

    function it_has_source_account_number()
    {
        $this->getSourceAccountNumber()->__toString()->shouldReturn('123ABC');
    }

    function it_has_target_account_number()
    {
        $this->getTargetAccountNumber()->__toString()->shouldReturn('456DEF');
    }

    function it_has_amount()
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_will_be_created_in_started_state()
    {
        $this->getState()->shouldReturn('initialized');
    }

    function it_will_change_state_from_started_to_withdrawing()
    {
        $this->withdrawFromSource();
        $this->getState()->shouldReturn('withdrawing');
    }

    function it_will_change_state_from_withdrawing_to_withdrawn()
    {
        $this->withdrawFromSource();
        $this->withdrawnFromSource();
        $this->getState()->shouldReturn('withdrawn');
    }

    function it_will_change_state_from_withdrawn_to_depositing()
    {
        $this->withdrawFromSource();
        $this->withdrawnFromSource();
        $this->depositOnTarget();
        $this->getState()->shouldReturn('depositing');
    }

    function it_will_change_state_from_depositing_to_deposited()
    {
        $this->withdrawFromSource();
        $this->withdrawnFromSource();
        $this->depositOnTarget();
        $this->depositedOnTarget();
        $this->getState()->shouldReturn('deposited');
    }

    function it_will_change_state_from_deposited_to_finalized()
    {
        $this->withdrawFromSource();
        $this->withdrawnFromSource();
        $this->depositOnTarget();
        $this->depositedOnTarget();
        $this->finalize();
        $this->getState()->shouldReturn('finalized');
    }
}
