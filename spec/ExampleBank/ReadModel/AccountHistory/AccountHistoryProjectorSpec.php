<?php

namespace spec\ExampleBank\ReadModel\AccountHistory;

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

class AccountHistoryProjectorSpec extends ObjectBehavior
{
    function it_creates_account_history_when_account_was_opened()
    {
        $domainEvent = new AccountOpenedEvent(AccountNumber::fromString('123ABC'));
        $event = AccountOpenedEnvelope::wrap($domainEvent);

        $this->onAccountOpened($event);

        $this->getAccount('123ABC')->getAccountNumber()->shouldReturn('123ABC');
    }

    function it_throws_exception_when_trying_to_reopen_opened_account()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getAccount', ['123ABC']);
    }

    function it_throws_exception_when_trying_to_re_open_same_account()
    {
        $domainEvent = new AccountOpenedEvent(AccountNumber::fromString('123ABC'));
        $event = AccountOpenedEnvelope::wrap($domainEvent);

        $this->onAccountOpened($event);
        $this->shouldThrow(\InvalidArgumentException::class)->during('onAccountOpened', [$event]);
    }

    function it_appends_deposit_entry_when_money_were_deposited()
    {
        $accountNumber = AccountNumber::fromString('123ABC');

        $openAccount = new AccountOpenedEvent($accountNumber);
        $this->onAccountOpened(AccountOpenedEnvelope::wrap($openAccount));

        $deposit = new MoneyWereDepositedEvent(
            $accountNumber,
            new Deposit(
                Transaction::generate(),
                100
            )
        );
        $this->onMoneyWereDeposited(MoneyWereDepositedEnvelope::wrap($deposit));

        $this->getAccount('123ABC')
            ->getHistory()[1]
            ->getAmount()
            ->shouldReturn(100);
    }

    function it_appends_deposit_entry_when_money_were_withdrawn()
    {
        $accountNumber = AccountNumber::fromString('123ABC');

        $openAccount = new AccountOpenedEvent($accountNumber);
        $this->onAccountOpened(AccountOpenedEnvelope::wrap($openAccount));

        $withdraw = new MoneyWereWithdrawnEvent(
            $accountNumber,
            new Withdraw(
                Transaction::generate(),
                100
            )
        );
        $this->onMoneyWereWithdrawn(MoneyWereWithdrawnEnvelope::wrap($withdraw));

        $this->getAccount('123ABC')
            ->getHistory()[1]
            ->getAmount()
            ->shouldReturn(100);
    }
}
