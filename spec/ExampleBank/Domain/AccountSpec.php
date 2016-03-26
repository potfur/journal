<?php

namespace spec\ExampleBank\Domain;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;
use ExampleBank\Domain\Withdraw;
use ExampleBank\DomainEvent\AccountOpenedEvent;
use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('open', [AccountNumber::fromString('123ABC')]);
    }

    function it_has_number()
    {
        $this->getNumber()->__toString()->shouldReturn('123ABC');
    }

    function it_has_a_balance()
    {
        $this->getBalance()->shouldReturn(0);
    }

    function it_will_increase_balance_trough_deposit()
    {
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $this->deposit($deposit);
        $this->getBalance()->shouldReturn(100);
    }

    function it_will_throw_exception_when_trying_to_withdraw_more_than_balance()
    {
        $withdraw = new Withdraw(
            Transaction::fromString('000'),
            100
        );

        $this->shouldThrow(\InvalidArgumentException::class)->during('withdraw', [$withdraw]);
    }

    function it_will_decrease_balance_trough_withdraw()
    {
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $withdraw = new Withdraw(
            Transaction::fromString('000'),
            100
        );

        $this->beConstructedThrough('open', [AccountNumber::fromString('123ABC')]);
        $this->deposit($deposit);
        $this->withdraw($withdraw);
        $this->getBalance()->shouldReturn(0);
    }

    function it_will_store_account_opened_event()
    {
        $this->beConstructedThrough('open', [AccountNumber::fromString('123ABC')]);
        $this->getRecordedEvents()[0]->shouldBeAnInstanceOf(AccountOpenedEvent::class);
    }

    function it_will_store_money_deposited_event()
    {
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $this->beConstructedThrough('open', [AccountNumber::fromString('123ABC')]);
        $this->deposit($deposit);

        $this->getRecordedEvents()[1]->shouldBeAnInstanceOf(MoneyWereDepositedEvent::class);
    }

    function it_will_store_money_withdrawn_event()
    {
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $withdraw = new Withdraw(
            Transaction::fromString('000'),
            100
        );

        $this->deposit($deposit);
        $this->withdraw($withdraw);

        $this->getRecordedEvents()[2]->shouldBeAnInstanceOf(MoneyWereWithdrawnEvent::class);
    }

    function it_will_store_transfer_requested_event()
    {
        $deposit = new Deposit(
            Transaction::fromString('000'),
            100
        );

        $transfer = new Transfer(
            Transaction::fromString('000'),
            AccountNumber::fromString('456DEF'),
            100
        );

        $this->deposit($deposit);
        $this->transfer($transfer);

        $this->getRecordedEvents()[2]->shouldBeAnInstanceOf(MoneyTransferRequestedEvent::class);
    }
}
