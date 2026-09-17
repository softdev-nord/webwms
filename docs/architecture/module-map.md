# Module map

## Inbound

Owns supplier-facing inbound orders, delivery notices, goods receipts, receipt
discrepancies and inbound quality decisions. It requests stock postings from
Inventory and putaway transports from Fulfillment.

## Inventory

Owns products as stock-relevant references, warehouse topology, handling units,
stock balances, stock states and the immutable stock ledger. Inventory is the
only module allowed to commit physical stock changes.

## Fulfillment

Owns reservations, pick waves, pick tasks, replenishment tasks, relocation and
internal transport orders. It consumes availability information exposed by
Inventory but never writes stock tables directly.

## Outbound

Owns outbound orders, packing sessions, packages, shipments, tours and loading.
It requests allocations and picks from Fulfillment and publishes shipment
status through Integration.

## Platform

Provides reusable business capabilities such as document rendering, label and
print routing, attachments, media, KPI projections, workflow automation and
optional tenant billing.

## Administration

Owns tenants, business partners, sites, users, roles, permissions, number ranges
and feature configuration. Tenant identity is propagated explicitly into every
tenant-owned aggregate and asynchronous message.

## Integration

Owns API clients, credentials, import/export jobs, integration messages, outbox
delivery attempts and adapters for ERP, commerce, carriers and warehouse
hardware. It translates external contracts into application commands.

## Allowed synchronous dependencies

```text
Inbound ----> Inventory
Inbound ----> Fulfillment
Outbound ---> Fulfillment
Fulfillment -> Inventory
All modules -> Administration contracts
All modules -> Platform contracts
Integration -> Application contracts of all business modules
```

Business modules publish events to Integration. They must not depend on a
specific ERP, carrier, message broker or hardware protocol.
