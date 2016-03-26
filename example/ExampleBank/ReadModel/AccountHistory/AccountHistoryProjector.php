<?php

namespace ExampleBank\ReadModel\AccountHistory;

use ExampleBank\EventEmitter\Event\AccountOpenedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereDepositedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereWithdrawnEnvelope;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccountHistoryProjector implements EventSubscriberInterface
{
    /**
     * @var AccountHistory[]
     */
    private $accounts = [];

    public function getAccount($accountNumber)
    {
        if (!array_key_exists((string) $accountNumber, $this->accounts)) {
            throw new \InvalidArgumentException('Unknown account with number: ' . $accountNumber);
        }

        return $this->accounts[$accountNumber];
    }

    public static function getSubscribedEvents()
    {
        return [
            AccountOpenedEnvelope::getEventName() => 'onAccountOpened',
            MoneyWereDepositedEnvelope::getEventName() => 'onMoneyWereDeposited',
            MoneyWereWithdrawnEnvelope::getEventName() => 'onMoneyWereWithdrawn',
        ];
    }

    public function onAccountOpened(AccountOpenedEnvelope $event)
    {
        if (array_key_exists((string) $event->getAccountNumber(), $this->accounts)) {
            throw new \InvalidArgumentException('Duplicate AccountOpened event for ' . $event->getAccountNumber());
        }

        $this->accounts[(string) $event->getAccountNumber()] = new AccountHistory(
            $event->getAccountNumber(),
            $event->getTimestamp()
        );
    }

    public function onMoneyWereDeposited(MoneyWereDepositedEnvelope $event)
    {
        $this->accounts[(string) $event->getAccountNumber()]->deposit($event->getTimestamp(), $event->getAmount());
    }

    public function onMoneyWereWithdrawn(MoneyWereWithdrawnEnvelope $event)
    {
        $this->accounts[(string) $event->getAccountNumber()]->withdraw($event->getTimestamp(), $event->getAmount());
    }
}
