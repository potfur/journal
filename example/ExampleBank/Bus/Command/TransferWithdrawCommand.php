<?php

namespace ExampleBank\Bus\Command;

use ExampleBank\Bus\Assert\AssertAccountNumber;
use ExampleBank\Bus\Assert\AssertAmount;

final class TransferWithdrawCommand
{
    use AssertAccountNumber;
    use AssertAmount;

    private $accountNumber;

    private $amount;

    private $transaction;

    public function __construct($accountNumber, $amount, $transaction)
    {
        $this->assertAccountNumber($accountNumber);
        $this->assertAmount($amount);

        $this->accountNumber = $accountNumber;
        $this->amount = $amount;
        $this->transaction = $transaction;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }
}
