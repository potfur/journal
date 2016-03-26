<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\TransferDepositCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;

final class TransferDepositHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(TransferDepositCommand $command)
    {
        $account = $this->accounting->getAccount(AccountNumber::fromString($command->getAccountNumber()));
        $deposit = new Deposit(
            Transaction::fromString($command->getTransaction()),
            $command->getAmount()
        );

        $account->deposit($deposit);

        $this->accounting->commit();
    }
}
