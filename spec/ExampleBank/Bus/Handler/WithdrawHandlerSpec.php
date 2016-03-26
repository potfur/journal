<?php

namespace spec\ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\WithdrawCommand;
use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Withdraw;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WithdrawHandlerSpec extends ObjectBehavior
{
    function it_handles_withdraw_command(AccountingInterface $accounting, Account $account)
    {
        $account->withdraw(Argument::type(Withdraw::class))->shouldBeCalled();

        $accounting->getAccount(Argument::type(AccountNumber::class))->willReturn($account);
        $accounting->commit()->shouldBeCalled();

        $command = new WithdrawCommand('123ABC', 100);

        $this->beConstructedWith($accounting);
        $this->handle($command);
    }
}
