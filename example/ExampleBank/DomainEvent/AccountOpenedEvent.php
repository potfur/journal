<?php

namespace ExampleBank\DomainEvent;

use ExampleBank\Domain\AccountNumber;
use Journal\DomainEvent\DomainEvent;

final class AccountOpenedEvent implements DomainEvent
{
    private $accountNumber;

    private $identifier;

    private $recordedOn;

    public function __construct(AccountNumber $accountNumber)
    {
        $this->accountNumber = (string) $accountNumber;

        $this->identifier = uniqid();
        $this->recordedOn = microtime(true);
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
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
