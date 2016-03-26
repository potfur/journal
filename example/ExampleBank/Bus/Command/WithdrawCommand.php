<?php

namespace ExampleBank\Bus\Command;

use ExampleBank\Bus\Assert\AssertAccountNumber;
use ExampleBank\Bus\Assert\AssertAmount;

final class WithdrawCommand
{
    use AssertAccountNumber;
    use AssertAmount;

    private $accountNumber;

    private $amount;

    public function __construct($accountNumber, $amount)
    {
        $this->assertAccountNumber($accountNumber);
        $this->assertAmount($amount);

        $this->accountNumber = $accountNumber;
        $this->amount = $amount;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
