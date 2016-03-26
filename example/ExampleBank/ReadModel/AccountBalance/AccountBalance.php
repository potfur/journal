<?php

namespace ExampleBank\ReadModel\AccountBalance;

class AccountBalance
{
    private $accountNumber;

    private $balance = 0;

    public function __construct($accountNumber)
    {
        $this->accountNumber = $accountNumber;
        $this->balance = 0;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function increaseBy($amount)
    {
        $this->balance += $amount;
    }

    public function decreaseBy($amount)
    {
        $this->balance -= $amount;
    }
}
