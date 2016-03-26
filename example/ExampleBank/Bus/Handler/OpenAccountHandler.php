<?php

namespace ExampleBank\Bus\Handler;

use ExampleBank\AccountingInterface;
use ExampleBank\Bus\Command\OpenAccountCommand;
use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;

final class OpenAccountHandler
{
    private $accounting;

    public function __construct(AccountingInterface $accounting)
    {
        $this->accounting = $accounting;
    }

    public function handle(OpenAccountCommand $command)
    {
        $account = Account::open(
            AccountNumber::fromString($command->getAccountNumber())
        );

        $this->accounting->addAccount($account);
        $this->accounting->commit();
    }
}
