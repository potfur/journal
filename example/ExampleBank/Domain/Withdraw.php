<?php

namespace ExampleBank\Domain;

final class Withdraw
{
    private $transaction;

    private $amount;

    public function __construct(Transaction $transaction, $amount)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must greater than 0');
        }

        $this->transaction = $transaction;
        $this->amount = $amount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
