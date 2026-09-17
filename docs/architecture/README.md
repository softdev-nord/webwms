# WebWMS 3.0 architecture

WebWMS 3.0 is rebuilt as a modular monolith. Each business module owns its
domain model, application use cases and infrastructure adapters. Cross-module
communication happens through explicit application contracts or domain events.

## Runtime baseline

- PHP 8.4 or newer
- Symfony 8.1
- Doctrine ORM and DBAL
- MariaDB 10.6
- Symfony Messenger with RabbitMQ for asynchronous integration
- API Platform for external HTTP APIs

The dependency upgrade is performed separately from the architecture bootstrap
so that `composer.lock` is only changed in an environment where Composer, PHP
and the complete quality suite are available.

## Modules

| Module | Responsibility |
| --- | --- |
| Inbound | Purchase orders, advance shipping notices, goods receipts, quality checks and putaway |
| Inventory | Warehouse topology, stock ledger, batches, serial numbers, expiry dates and stocktaking |
| Fulfillment | Allocation, picking, replenishment and internal transports |
| Outbound | Packing, shipping, loading, tours and carrier handover |
| Platform | Documents, printing, media, KPIs, automation and billing extensions |
| Administration | Tenants, sites, users, roles, permissions and feature configuration |
| Integration | ERP, shop, carrier, hardware and warehouse-control adapters |

## Dependency rules

1. Domain code does not depend on Symfony, Doctrine or infrastructure code.
2. Application code coordinates use cases and depends on domain abstractions.
3. Infrastructure code implements ports owned by domain or application layers.
4. UI and API controllers contain no business logic.
5. Modules do not access another module's persistence model directly.
6. State-changing integrations use an outbox and idempotent consumers.
7. Every stock-changing use case writes an immutable stock-ledger entry.

See [the module map](module-map.md) and the architecture decisions in
[`adr/`](adr/).
