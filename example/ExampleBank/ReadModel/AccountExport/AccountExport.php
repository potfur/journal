<?php

namespace ExampleBank\ReadModel\AccountExport;

class AccountExport
{
    const OPENED = '=';
    const DEPOSITED = '+';
    const WITHDRAWN = '-';

    private $accountNumber;

    private $balance = 0;

    private $history = [];

    public function __construct($accountNumber, $timestamp)
    {
        $this->accountNumber = $accountNumber;
        $this->history[] = [
            $timestamp,
            self::OPENED,
            0,
            $this->balance
        ];
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function increaseBy($amount, $timestamp)
    {
        $this->balance += $amount;
        $this->appendEntry($amount, $timestamp, self::DEPOSITED);
    }

    public function decreaseBy($amount, $timestamp)
    {
        $this->balance -= $amount;
        $this->appendEntry($amount, $timestamp, self::WITHDRAWN);
    }

    private function appendEntry($amount, $timestamp, $operation)
    {
        $this->history[] = [
            $timestamp,
            $operation,
            $amount,
            $this->balance
        ];
    }
}
