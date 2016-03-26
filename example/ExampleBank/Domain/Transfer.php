<?php

namespace ExampleBank\Domain;

final class Transfer
{
    private $transaction;

    private $target;

    private $amount;

    public function __construct(Transaction $transaction, AccountNumber $target, $amount)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must greater than 0');
        }

        $this->transaction = $transaction;
        $this->target = $target;
        $this->amount = $amount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getTargetAccountNumber()
    {
        return $this->target;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
