<?php

namespace ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transfer;
use Journal\DomainEvent\DomainEvent;

final class MoneyTransferRequestedEvent implements DomainEvent
{
    private $source;

    private $target;

    private $transaction;

    private $amount;

    private $identifier;

    private $recordedOn;

    public function __construct(AccountNumber $source, Transfer $transfer)
    {
        $this->source = (string) $source;
        $this->target = (string) $transfer->getTargetAccountNumber();
        $this->transaction = (string) $transfer->getTransaction();
        $this->amount = (int) $transfer->getAmount();

        $this->identifier = uniqid();
        $this->recordedOn = microtime(true);
    }

    public function getSourceAccountNumber()
    {
        return $this->source;
    }

    public function getTargetAccountNumber()
    {
        return $this->target;
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
