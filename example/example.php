<?php

use ExampleBank\Accounting;
use ExampleBank\Bus\Command\OpenAccountCommand;
use ExampleBank\Bus\Command\DepositCommand;
use ExampleBank\Bus\Command\TransferCommand;
use ExampleBank\Bus\Command\TransferDepositCommand;
use ExampleBank\Bus\Command\TransferWithdrawCommand;
use ExampleBank\Bus\Command\WithdrawCommand;
use ExampleBank\Bus\Handler\TransferDepositHandler;
use ExampleBank\Bus\Handler\TransferWithdrawHandler;
use ExampleBank\Domain\Account;
use ExampleBank\EventEmitter\DispatcherEmitter;
use ExampleBank\Bus\Handler\OpenAccountHandler;
use ExampleBank\Bus\Handler\DepositHandler;
use ExampleBank\Bus\Handler\TransferHandler;
use ExampleBank\Bus\Handler\WithdrawHandler;
use ExampleBank\ProcessManager\TransferManager;
use ExampleBank\ReadModel\AccountBalance\AccountBalanceProjector;
use ExampleBank\ReadModel\AccountExport\CSV;
use ExampleBank\ReadModel\AccountHistory\AccountHistoryProjector;
use ExampleBank\ReadModel\AccountExport\AccountExportProjector;
use Journal\Contract\Contract;
use Journal\DomainEvent\EventCollection;
use Journal\EventStore\EventStore;
use Journal\EventStore\EventStreamIdentifier;
use Journal\Persistence\InMemory;
use Journal\UnitOfWork\UnitOfWork;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;
use Symfony\Component\EventDispatcher\EventDispatcher;

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$persistence = new InMemory();
$eventStore = new EventStore($persistence);
$dispatcher = new EventDispatcher();
$eventEmitter = new DispatcherEmitter($dispatcher);
$unitOfWork = new UnitOfWork($eventStore, $eventEmitter);
$accounting = new Accounting($unitOfWork);

$locator = new InMemoryLocator();
$bus = new CommandBus(
    [
        new LockingMiddleware(),
        new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            $locator,
            new HandleInflector()
        ),
    ]
);

$locator->addHandler(new OpenAccountHandler($accounting), OpenAccountCommand::class);
$locator->addHandler(new DepositHandler($accounting), DepositCommand::class);
$locator->addHandler(new WithdrawHandler($accounting), WithdrawCommand::class);
$locator->addHandler(new TransferHandler($accounting), TransferCommand::class);
$locator->addHandler(new TransferDepositHandler($accounting), TransferDepositCommand::class);
$locator->addHandler(new TransferWithdrawHandler($accounting), TransferWithdrawCommand::class);

$accountHistory = new AccountHistoryProjector();
$accountBalance = new AccountBalanceProjector();
$transferManager = new TransferManager($bus);

$dispatcher->addSubscriber($accountHistory);
$dispatcher->addSubscriber($accountBalance);
$dispatcher->addSubscriber($transferManager);

/*
 * Normal event propagation
 *
 * When commands trigger actions on Account, domain events will be emitted
 * As reaction to those events, projectors will create data for data models
 */

$bus->handle(new OpenAccountCommand('Foo', 'EUR'));
$bus->handle(new DepositCommand('Foo', 20));
$bus->handle(new OpenAccountCommand('Bar', 'EUR'));
$bus->handle(new TransferCommand('Foo', 'Bar', 10));
$bus->handle(new WithdrawCommand('Foo', 10));
$bus->handle(new WithdrawCommand('Bar', 10));

var_dump($accountBalance, $accountHistory, $transferManager, $persistence);

/*
 * Introducing new projector
 *
 * New projector needs to catch up
 * Separate emitter is created - just for new projector - it will emit all historical events
 */

$dispatcher = new EventDispatcher();
$eventEmitter = new DispatcherEmitter($dispatcher);

$export = new AccountExportProjector();
$dispatcher->addSubscriber($export);

$stream = $eventStore->openStream(
    Contract::fromClass(Account::class),
    EventStreamIdentifier::fromString('Foo')
);

$eventEmitter->emit(new EventCollection($stream->all()));

$stream = $eventStore->openStream(
    Contract::fromClass(Account::class),
    EventStreamIdentifier::fromString('Bar')
);

$eventEmitter->emit(new EventCollection($stream->all()));
var_dump($export);
