<?php

namespace spec\ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\TransferWithdrawCommand;
use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Withdraw;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferWithdrawHandlerSpec extends ObjectBehavior
{
    function it_handles_transfer_withdraw_command(AccountingInterface $accounting, Account $account)
    {
        $account->withdraw(Argument::type(Withdraw::class))->shouldBeCalled();

        $accounting->getAccount(Argument::type(AccountNumber::class))->willReturn($account);
        $accounting->commit()->shouldBeCalled();

        $command = new TransferWithdrawCommand('123ABC', 100, Transaction::fromString('000'));

        $this->beConstructedWith($accounting);
        $this->handle($command);
    }
}
