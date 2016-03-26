<?php

namespace ExampleBank\Domain;

use Journal\Identifier\Identifier;

final class AccountNumber implements Identifier
{
    private $number;

    private function __construct($accountNumber)
    {
        if (!is_string($accountNumber)) {
            throw new \InvalidArgumentException('Account number must be a string');
        }

        $this->number = $accountNumber;
    }

    public static function fromString($accountNumber)
    {
        return new self($accountNumber);
    }

    public function equals(Identifier $accountNumber)
    {
        return (string) $this == (string) $accountNumber;
    }

    public function __toString()
    {
        return (string) $this->number;
    }
}
