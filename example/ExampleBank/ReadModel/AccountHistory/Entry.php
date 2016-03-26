<?php

namespace ExampleBank\ReadModel\AccountHistory;

class Entry
{
    private $description;

    private $amount;

    private $date;

    public function __construct($timestamp, $amount, $description)
    {
        if (!is_numeric($timestamp)) {
            throw new \InvalidArgumentException('Timestamp must be a valid numeric');
        }

        if (!is_int($amount)) {
            throw new \InvalidArgumentException('Amount must be an integer');
        }

        $this->date = new \DateTimeImmutable('@' . (int) $timestamp);
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDate()
    {
        return $this->date;
    }
}
