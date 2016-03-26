<?php

namespace ExampleBank\Domain;

use ExampleBank\DomainEvent\AccountOpenedEvent;
use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use Journal\AggregateRoot\AggregateRoot;
use Journal\DomainEvent\DomainEvent;
use Journal\DomainEvent\DomainEvents;

class Account implements AggregateRoot
{
    /**
     * @var AccountNumber
     */
    private $number;

    /**
     * @var int
     */
    private $balance;

    /**
     * @var DomainEvent[]
     */
    private $record = [];

    private function __construct()
    {
    }

    public static function open(AccountNumber $number)
    {
        $account = new static();
        $account->applyAccountOpenedEvent($account->record(new AccountOpenedEvent($number)));

        return $account;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function deposit(Deposit $deposit)
    {
        $this->applyMoneyWereDepositedEvent(
            $this->record(
                new MoneyWereDepositedEvent(
                    $this->number,
                    $deposit
                )
            )
        );
    }

    public function withdraw(Withdraw $withdraw)
    {
        if ($this->balance < $withdraw->getAmount()) {
            throw new \InvalidArgumentException('Trying to withdraw greater amount than current balance');
        }

        $this->applyMoneyWereWithdrawnEvent(
            $this->record(
                new MoneyWereWithdrawnEvent(
                    $this->number,
                    $withdraw
                )
            )
        );
    }

    public function transfer(Transfer $transfer)
    {
        if ($this->number->equals($transfer->getTargetAccountNumber())) {
            throw new \InvalidArgumentException('Trying to transfer to same account');
        }

        $this->record(
            new MoneyTransferRequestedEvent(
                $this->number,
                $transfer
            )
        );
    }

    public function getRecordedEvents()
    {
        return $this->record;
    }

    private function record($event)
    {
        $this->record[] = $event;

        return $event;
    }

    public function clearRecordedEvents()
    {
        $this->record = [];
    }

    public static function reconstituteFrom(DomainEvents $events)
    {
        $aggregate = new static();
        $aggregate->clearRecordedEvents();

        foreach ($events as $event) {
            $aggregate->handleEvent($aggregate, $event);
        }

        return $aggregate;
    }

    private function handleEvent(Account $aggregate, DomainEvent $event)
    {
        $handlingMethod = $aggregate->getHandlerMethodFor($event);

        if (!method_exists($aggregate, $handlingMethod)) {
            throw new \InvalidArgumentException('Unknown event received: ' . get_class($event));
        }

        $aggregate->$handlingMethod($event);
    }

    private function getHandlerMethodFor(DomainEvent $event)
    {
        return 'apply' . substr(strrchr('\\' . get_class($event), '\\'), 1);
    }

    private function applyAccountOpenedEvent(AccountOpenedEvent $event)
    {
        $this->number = AccountNumber::fromString($event->getAccountNumber());
        $this->balance = 0;
    }

    private function applyMoneyWereDepositedEvent(MoneyWereDepositedEvent $event)
    {
        $this->balance += $event->getAmount();
    }

    private function applyMoneyWereWithdrawnEvent(MoneyWereWithdrawnEvent $event)
    {
        $this->balance -= $event->getAmount();
    }

    private function applyMoneyTransferRequestedEvent(MoneyTransferRequestedEvent $event)
    {
        // NOP
    }
}
