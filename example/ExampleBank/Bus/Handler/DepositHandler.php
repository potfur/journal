<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\DepositCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;

final class DepositHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(DepositCommand $command)
    {
        $account = $this->accounting->getAccount(AccountNumber::fromString($command->getAccountNumber()));
        $deposit = new Deposit(
            Transaction::generate(),
            $command->getAmount()
        );

        $account->deposit($deposit);

        $this->accounting->commit();
    }
}
