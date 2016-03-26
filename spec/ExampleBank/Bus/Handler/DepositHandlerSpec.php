<?php

namespace spec\ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\DepositCommand;
use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DepositHandlerSpec extends ObjectBehavior
{
    function it_handles_deposit_command(AccountingInterface $accounting, Account $account)
    {
        $account->deposit(Argument::type(Deposit::class))->shouldBeCalled();

        $accounting->getAccount(Argument::type(AccountNumber::class))->willReturn($account);
        $accounting->commit()->shouldBeCalled();

        $command = new DepositCommand('123ABC', 100);

        $this->beConstructedWith($accounting);
        $this->handle($command);
    }
}
