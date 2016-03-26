<?php

namespace ExampleBank\Bus\Command;

use ExampleBank\Bus\Assert\AssertAccountNumber;
use ExampleBank\Bus\Assert\AssertAmount;

final class TransferCommand
{
    use AssertAccountNumber;
    use AssertAmount;

    private $sourceAccountNumber;

    private $targetAccountNumber;

    private $amount;

    public function __construct($sourceAccountNumber, $targetAccountNumber, $amount)
    {
        $this->assertAccountNumber($sourceAccountNumber);
        $this->assertAccountNumber($targetAccountNumber);
        $this->assertAmount($amount);

        $this->sourceAccountNumber = $sourceAccountNumber;
        $this->targetAccountNumber = $targetAccountNumber;
        $this->amount = $amount;
    }

    public function getSourceAccountNumber()
    {
        return $this->sourceAccountNumber;
    }

    public function getTargetAccountNumber()
    {
        return $this->targetAccountNumber;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
