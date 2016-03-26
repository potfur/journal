<?php

namespace ExampleBank\Bus\Command;

use ExampleBank\Bus\Assert\AssertAccountNumber;

final class OpenAccountCommand
{
    use AssertAccountNumber;

    private $accountNumber;

    public function __construct($accountNumber)
    {
        $this->assertAccountNumber($accountNumber);

        $this->accountNumber = $accountNumber;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }
}
