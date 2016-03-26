<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\TransferCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;

final class TransferHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(TransferCommand $command)
    {
        $account = $this->accounting->getAccount(AccountNumber::fromString($command->getSourceAccountNumber()));
        $transfer = new Transfer(
            Transaction::generate(),
            AccountNumber::fromString($command->getTargetAccountNumber()),
            $command->getAmount()
        );

        $account->transfer($transfer);

        $this->accounting->commit();
    }
}
