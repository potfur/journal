<?php

namespace spec\ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\TransferDepositCommand;
use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferDepositHandlerSpec extends ObjectBehavior
{
    function it_handles_transfer_deposit_command(AccountingInterface $accounting, Account $account)
    {
        $account->deposit(Argument::type(Deposit::class))->shouldBeCalled();

        $accounting->getAccount(Argument::type(AccountNumber::class))->willReturn($account);
        $accounting->commit()->shouldBeCalled();

        $command = new TransferDepositCommand('123ABC', 100, Transaction::fromString('000'));

        $this->beConstructedWith($accounting);
        $this->handle($command);
    }
}
