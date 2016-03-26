<?php

namespace ExampleBank\Domain;

use Journal\Identifier\Identifier;

final class Transaction implements Identifier
{
    private $identifier;

    private function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public static function generate()
    {
        return new static((string) uniqid('transaction-'));
    }

    public static function fromString($string)
    {
        return new static($string);
    }

    public function __toString()
    {
        return $this->identifier;
    }

    public function equals(Identifier $other)
    {
        return (string) $this == (string) $other;
    }
}
