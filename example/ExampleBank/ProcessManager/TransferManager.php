<?php

namespace ExampleBank\ProcessManager;

use ExampleBank\Bus\Command\TransferDepositCommand;
use ExampleBank\Bus\Command\TransferWithdrawCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\TransferProcess;
use ExampleBank\EventEmitter\Event\MoneyTransferRequestedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereDepositedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereWithdrawnEnvelope;
use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransferManager implements EventSubscriberInterface
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var TransferProcess[]
     */
    private $process = [];

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public static function getSubscribedEvents()
    {
        return [
            MoneyWereWithdrawnEnvelope::getEventName() => 'onWithdrawnFromSource',
            MoneyWereDepositedEnvelope::getEventName() => 'onDepositedOnTarget',
            MoneyTransferRequestedEnvelope::getEventName() => 'onTransferRequested',
        ];
    }

    public function onTransferRequested(MoneyTransferRequestedEnvelope $event)
    {
        $this->assertProcessWasNotStarted($event->getTransaction());

        $process = new TransferProcess(
            Transaction::fromString($event->getTransaction()),
            AccountNumber::fromString($event->getSourceAccountNumber()),
            AccountNumber::fromString($event->getTargetAccountNumber()),
            $event->getAmount()
        );

        $process->withdrawFromSource();

        $this->bus->handle(
            new TransferWithdrawCommand(
                (string) $process->getSourceAccountNumber(),
                (int) $process->getAmount(),
                (string) $process->getTransaction()
            )
        );

        $this->process[$event->getTransaction()] = $process;
    }

    private function assertProcessWasNotStarted($transaction)
    {
        if (array_key_exists($transaction, $this->process)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Transfer with identifier %s is already in progress',
                    $transaction
                )
            );
        }
    }

    public function onWithdrawnFromSource(MoneyWereWithdrawnEnvelope $event)
    {
        if (!$process = $this->getProcessForTransaction($event->getTransaction())) {
            return;
        }

        $this->assertFinalized($process);

        $process->withdrawnFromSource();
        $process->depositOnTarget();

        $this->bus->handle(
            new TransferDepositCommand(
                (string) $process->getTargetAccountNumber(),
                (int) $process->getAmount(),
                (string) $process->getTransaction()
            )
        );
    }

    public function onDepositedOnTarget(MoneyWereDepositedEnvelope $event)
    {
        if (!$process = $this->getProcessForTransaction($event->getTransaction())) {
            return;
        }

        $this->assertFinalized($process);

        $process->depositedOnTarget();
        $process->finalize();
    }

    public function getProcessForTransaction($transaction)
    {
        return array_key_exists($transaction, $this->process) ? $this->process[$transaction] : null;
    }

    private function assertFinalized(TransferProcess $process)
    {
        if ($process->isFinalized()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unexpected event, transfer with identifier %s is already finalized',
                    $process->getTransaction()
                )
            );
        }
    }
}
