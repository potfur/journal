<?php

namespace ExampleBank;

use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;

interface AccountingInterface
{
    /**
     * @param Account $account
     *
     * @return void
     */
    public function addAccount(Account $account);

    /**
     * @param AccountNumber $accountNumber
     *
     * @return Account
     */
    public function getAccount(AccountNumber $accountNumber);

    /**
     * @return void
     */
    public function commit();
}
