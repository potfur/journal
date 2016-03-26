<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\TransferWithdrawCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Withdraw;

final class TransferWithdrawHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(TransferWithdrawCommand $command)
    {
        $account = $this->accounting->getAccount(AccountNumber::fromString($command->getAccountNumber()));
        $withdraw = new Withdraw(
            Transaction::fromString($command->getTransaction()),
            $command->getAmount()
        );

        $account->withdraw($withdraw);

        $this->accounting->commit();
    }
}
