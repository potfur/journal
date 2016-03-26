<?php

namespace spec\ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\OpenAccountCommand;
use ExampleBank\Domain\Account;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OpenAccountHandlerSpec extends ObjectBehavior
{
    function it_handles_create_account_command(AccountingInterface $accounting)
    {
        $accounting->addAccount(Argument::type(Account::class))->shouldBeCalled();
        $accounting->commit()->shouldBeCalled();

        $command = new OpenAccountCommand('123ABC');

        $this->beConstructedWith($accounting);
        $this->handle($command);
    }
}
