<?php

namespace spec\ExampleBank\ProcessManager;

use ExampleBank\Bus\Command\TransferDepositCommand;
use ExampleBank\Bus\Command\TransferWithdrawCommand;
use ExampleBank\Domain\AccountNumber;
use ExampleBank\Domain\Deposit;
use ExampleBank\Domain\Transaction;
use ExampleBank\Domain\Transfer;
use ExampleBank\Domain\Withdraw;
use ExampleBank\DomainEvent\MoneyTransferRequestedEvent;
use ExampleBank\DomainEvent\MoneyWereDepositedEvent;
use ExampleBank\DomainEvent\MoneyWereWithdrawnEvent;
use ExampleBank\EventEmitter\Event\MoneyTransferRequestedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereDepositedEnvelope;
use ExampleBank\EventEmitter\Event\MoneyWereWithdrawnEnvelope;
use League\Tactician\CommandBus;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferManagerSpec extends ObjectBehavior
{
    function let(CommandBus $bus)
    {
        $this->beConstructedWith($bus);
    }

    function it_will_start_transfer_process_when_transfer_requested(CommandBus $bus)
    {
        $this->onTransferRequested($this->create_money_transfer_requested_envelope());

        $command = new TransferWithdrawCommand('123ABC', 100, '000');
        $bus->handle($command)->shouldHaveBeenCalled();
    }

    function create_money_transfer_requested_envelope()
    {
        return MoneyTransferRequestedEnvelope::wrap(
            new MoneyTransferRequestedEvent(
                AccountNumber::fromString('123ABC'),
                new Transfer(
                    Transaction::fromString('000'),
                    AccountNumber::fromString('456DEF'),
                    100
                )
            )
        );
    }

    function it_will_throw_exception_when_trying_to_start_already_started_process()
    {
        $this->onTransferRequested($this->create_money_transfer_requested_envelope());
        $this->shouldThrow(\InvalidArgumentException::class)
            ->during(
                'onTransferRequested',
                [$this->create_money_transfer_requested_envelope()]
            );
    }

    function it_will_withdraw_from_source_when_transfer_requested(CommandBus $bus)
    {
        $this->onTransferRequested($this->create_money_transfer_requested_envelope());
        $this->onWithdrawnFromSource($this->create_money_transfer_withdrawn_envelope());

        $command = new TransferDepositCommand('456DEF', 100, '000');
        $bus->handle($command)->shouldHaveBeenCalled();
    }

    function create_money_transfer_withdrawn_envelope()
    {
        return MoneyWereWithdrawnEnvelope::wrap(
            new MoneyWereWithdrawnEvent(
                AccountNumber::fromString('123ABC'),
                new Withdraw(
                    Transaction::fromString('000'),
                    100
                )
            )
        );
    }

    function it_will_deposit_on_target_when_withdraw_completed(CommandBus $bus)
    {
        $this->onTransferRequested($this->create_money_transfer_requested_envelope());
        $this->onWithdrawnFromSource($this->create_money_transfer_withdrawn_envelope());
        $this->onDepositedOnTarget($this->create_money_transfer_deposit_envelope());

        $command = new TransferDepositCommand('456DEF', 100, '000');
        $bus->handle($command)->shouldHaveBeenCalled();
    }

    function create_money_transfer_deposit_envelope()
    {
        return MoneyWereDepositedEnvelope::wrap(
            new MoneyWereDepositedEvent(
                AccountNumber::fromString('123ABC'),
                new Deposit(
                    Transaction::fromString('000'),
                    100
                )
            )
        );
    }

    function it_will_throw_exception_when_receiving_event_from_finalized_process()
    {
        $this->onTransferRequested($this->create_money_transfer_requested_envelope());
        $this->onWithdrawnFromSource($this->create_money_transfer_withdrawn_envelope());
        $this->onDepositedOnTarget($this->create_money_transfer_deposit_envelope());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during(
                'onDepositedOnTarget',
                [$this->create_money_transfer_deposit_envelope()]
            );
    }
}
