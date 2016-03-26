<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\WithdrawCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Withdraw;

final class WithdrawHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(WithdrawCommand $command)
    {
        $account = $this->accounting->getAccount(AccountNumber::fromString($command->getAccountNumber()));
        $withdraw = new Withdraw(
            Transaction::generate(),
            $command->getAmount()
        );

        $account->withdraw($withdraw);

        $this->accounting->commit();
    }
}
