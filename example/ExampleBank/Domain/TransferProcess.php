<?php

namespace ExampleBank\Domain;

class TransferProcess
{
    const STATE_INITIALIZED = 'initialized';
    const STATE_WITHDRAWING = 'withdrawing';
    const STATE_WITHDRAWN = 'withdrawn';
    const STATE_DEPOSITING = 'depositing';
    const STATE_DEPOSITED = 'deposited';
    const STATE_FINALIZED = 'finalized';

    private $state = self::STATE_INITIALIZED;

    private $transition = [
        self::STATE_INITIALIZED => self::STATE_WITHDRAWING,
        self::STATE_WITHDRAWING => self::STATE_WITHDRAWN,
        self::STATE_WITHDRAWN => self::STATE_DEPOSITING,
        self::STATE_DEPOSITING => self::STATE_DEPOSITED,
        self::STATE_DEPOSITED => self::STATE_FINALIZED
    ];

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var AccountNumber
     */
    private $source;

    /**
     * @var AccountNumber
     */
    private $target;

    /**
     * @var int
     */
    private $amount;

    public function __construct(
        Transaction $transaction,
        AccountNumber $source,
        AccountNumber $target,
        $amount
    ) {
        $this->transaction = $transaction;
        $this->source = $source;
        $this->target = $target;
        $this->amount = $amount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getSourceAccountNumber()
    {
        return $this->source;
    }

    public function getTargetAccountNumber()
    {
        return $this->target;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function withdrawFromSource()
    {
        $this->changeState(self::STATE_WITHDRAWING);
    }

    public function withdrawnFromSource()
    {
        $this->changeState(self::STATE_WITHDRAWN);
    }

    public function depositOnTarget()
    {
        $this->changeState(self::STATE_DEPOSITING);
    }

    public function depositedOnTarget()
    {
        $this->changeState(self::STATE_DEPOSITED);
    }

    public function finalize()
    {
        $this->changeState(self::STATE_FINALIZED);
    }

    private function changeState($state)
    {
        if ($this->transition[$this->state] !== $state) {
            throw new \InvalidArgumentException('Invalid state transition ' . $state);
        }

        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function isFinalized()
    {
        return $this->state === self::STATE_FINALIZED;
    }
}
