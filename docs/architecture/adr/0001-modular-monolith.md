# ADR 0001: Use a modular monolith

- Status: Accepted
- Date: 2026-09-17

## Context

WebWMS 3.0 covers tightly connected warehouse transactions. Stock allocation,
picking, goods receipt and shipping require consistent transactional behaviour,
while integrations and hardware communication benefit from asynchronous
processing. Splitting those processes into independently deployed services at
the start would add distributed consistency and operational complexity before
the domain boundaries have stabilised.

## Decision

WebWMS 3.0 is implemented as a modular monolith with explicit Inbound,
Inventory, Fulfillment, Outbound, Platform, Administration and Integration
modules. Each module uses domain, application, infrastructure and UI layers.

Module boundaries are enforced in code review and static architecture tests.
Cross-module writes are forbidden. Asynchronous integration uses domain events,
an outbox and Symfony Messenger.

## Consequences

- One deployment and one primary transactional database remain possible.
- Core warehouse workflows can use local database transactions.
- Modules can evolve independently without premature network APIs.
- A module can later be extracted when its contracts and operational need are
proven.
