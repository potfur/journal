<?php

namespace spec\ExampleBank\ReadModel\AccountExport;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Withdraw;
use ExampleBank\DomainEvent\AccountOpenedEvent;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use ExampleBank\EventEmitter\Event\AccountOpenedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereDepositedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereWithdrawnEnvelope;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountExportProjectorSpec extends ObjectBehavior
{
    function it_creates_new_account_when_account_was_opened()
    {
        $domainEvent = new AccountOpenedEvent(AccountNumber::fromString('123ABC'));
        $event = AccountOpenedEnvelope::wrap($domainEvent);

        $this->onAccountOpened($event);

        $this->getAccount('123ABC')->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_throws_exception_when_trying_to_accesss_non_existing_account()
    {
        $domainEvent = new AccountOpenedEvent(AccountNumber::fromString('123ABC'));
        $event = AccountOpenedEnvelope::wrap($domainEvent);

        $this->onAccountOpened($event);
    }

    function it_throws_exception_when_reopening_already_opened_account()
    {
        $domainEvent = new AccountOpenedEvent(AccountNumber::fromString('123ABC'));
        $event = AccountOpenedEnvelope::wrap($domainEvent);

        $this->onAccountOpened($event);
        $this->shouldThrow(\InvalidArgumentException::class)->during('onAccountOpened', [$event]);
    }

    function it_increases_balance_when_money_were_deposited()
    {
        $accountNumber = AccountNumber::fromString('123ABC');

        $openAccount = new AccountOpenedEvent($accountNumber);
        $event = AccountOpenedEnvelope::wrap($openAccount);
        $this->onAccountOpened($event);

        $deposit = new MoneyWereDepositedEvent(
            $accountNumber,
            new Deposit(
                Transaction::generate(),
                100
            )
        );
        $event = MoneyWereDepositedEnvelope::wrap($deposit);
        $this->onMoneyWereDeposited($event);

        $this->getAccount('123ABC')
            ->getHistory()->shouldReturn(
                [
                    [$openAccount->getRecordedOn(), '=', 0, 0],
                    [$deposit->getRecordedOn(), '+', 100, 100]
                ]
            );
    }

    function it_decreases_balance_when_money_were_withdrawn()
    {
        $accountNumber = AccountNumber::fromString('123ABC');

        $openAccount = new AccountOpenedEvent($accountNumber);
        $event = AccountOpenedEnvelope::wrap($openAccount);
        $this->onAccountOpened($event);

        $withdraw = new MoneyWereWithdrawnEvent(
            $accountNumber,
            new Withdraw(
                Transaction::generate(),
                100
            )
        );
        $event = MoneyWereWithdrawnEnvelope::wrap($withdraw);
        $this->onMoneyWereWithdrawn($event);

        $this->getAccount('123ABC')
            ->getHistory()->shouldReturn(
                [
                    [$openAccount->getRecordedOn(), '=', 0, 0],
                    [$withdraw->getRecordedOn(), '-', 100, -100]
                ]
            );
    }
}
