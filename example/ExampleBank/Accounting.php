<?php

namespace ExampleBank;

use ExampleBank\Domain\Account;
use ExampleBank\Domain\AccountNumber;
use Journal\EventStore\EventStreamIdentifier;
use Journal\Contract\Contract;
use Journal\UnitOfWork\UnitOfWork;

final class Accounting implements AccountingInterface
{
    private $uow;

    public function __construct(UnitOfWork $uow)
    {
        $this->uow = $uow;
    }

    /**
     * {@inheritdoc}
     */
    public function addAccount(Account $account)
    {
        $this->uow->track(
            Contract::fromClass(Account::class),
            EventStreamIdentifier::fromString($account->getNumber()),
            $account
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccount(AccountNumber $accountNumber)
    {
        return $this->uow->get(
            Contract::fromClass(Account::class),
            EventStreamIdentifier::fromString($accountNumber)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->uow->commit();
    }
}
