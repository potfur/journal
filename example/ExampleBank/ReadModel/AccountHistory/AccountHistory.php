<?php

namespace ExampleBank\ReadModel\AccountHistory;

class AccountHistory
{
    const OPENED = 'Open';
    const DEPOSIT = 'Deposit';
    const WITHDRAW = 'Withdraw';

    private $accountNumber;

    private $history = [];

    public function __construct($accountNumber, $timestamp)
    {
        $this->accountNumber = $accountNumber;
        $this->append(
            new Entry(
                $timestamp,
                0,
                self::OPENED
            )
        );
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function withdraw($timestamp, $amount)
    {
        $this->append(
            new Entry(
                $timestamp,
                $amount,
                self::WITHDRAW
            )
        );
    }

    public function deposit($timestamp, $amount)
    {
        $this->append(
            new Entry(
                $timestamp,
                $amount,
                self::DEPOSIT
            )
        );
    }

    private function append(Entry $entry)
    {
        $this->history[] = $entry;
    }
}
