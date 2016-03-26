<?php

namespace ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use Journal\DomainEvent\DomainEvent;

final class MoneyWereDepositedEvent implements DomainEvent
{
    private $accountNumber;

    private $transaction;

    private $amount;

    private $identifier;

    private $recordedOn;

    public function __construct(AccountNumber $accountNumber, Deposit $deposit)
    {
        $this->accountNumber = (string) $accountNumber;
        $this->transaction = (string) $deposit->getTransaction();
        $this->amount = (int) $deposit->getAmount();

        $this->identifier = uniqid();
        $this->recordedOn = microtime(true);
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getRecordedOn()
    {
        return $this->recordedOn;
    }

    public function getVersion()
    {
        return 1;
    }
}
