Journal
=======

Heavily inspired by [EventCentric.Core] event sourcing framework.

## How does it work

[Example] is a simplified case of bank accounts with two basic actions: `Deposit` and `Withdraw` to increase and 
decrease balance.

There is also a composite process - `Transfer` to show how sagas/process managers can be implemented.


 - `Account` is a aggregate root, a class that is subject to actions from commands, 
 - each action that has effect on `Account` will create at least one `DomainEvent` instance, describing what happened, 
 eg. `MoneyWereDepositedEvent` which says that some amount of money were deposited on `Account` 
 - `Account` stores those events in order of occurrence,
 - `UnitOfWork` tracks events in `Account`, and when it is committed - all new events will be sent to `EventStore`,
 - `EventStore` will store new events as `EventStream` - an immutable list of changes for this account.
 - when new action occurs, `UnitOfWork` will reconstitute `Account` from stored events,
 - when ever event is stored, event emitter sends it to all presenters so they can react to it,
 - presenters `AccountHistoryPresenter` and `AccountBalancePresenter` subscribe to events and update data for read
  models, eg. as reaction to `MoneyWereDepositedEvent`, `AccountHistoryPresenter` will add new entry that says when and
   what amount was deposited. `AccountBalancePresenter` will increase accounts balance.


Example uses [Tactician] for command bus and [Event Dispatcher] for emitting events, but they can be replaced with
 anything else.


## Stuff that's missing

 - snapshots
 - more elaborate example


## License

See [LICENSE].


[EventCentric.Core]: https://github.com/event-centric/EventCentric.Core
[Example]: /example/
[Tactician]: https://github.com/thephpleague/tactician
[Event Dispatcher]: http://symfony.com/doc/current/components/event_dispatcher/introduction.html 
[LICENSE]: LICENSE
