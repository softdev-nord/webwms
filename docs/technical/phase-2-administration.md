# Phase 2: Mandanten und Standorte

## Mandanten

Das Aggregate `Administration\\Domain\\Tenant\\Tenant` verwaltet:

- UUID als stabile Identität;
- normalisierten Namen;
- Status `active` oder `inactive`;
- Erstellungs- und Änderungszeitpunkt;
- das Domain Event `TenantCreated`.

`CreateTenantHandler` verhindert doppelte IDs und persistiert über den
`TenantRepository`-Port. Der DBAL-Adapter schreibt transaktional nach
`wms_tenant`.

## Standorte

Ein Standort gehört exakt zu einem Mandanten. Er besitzt:

- UUID;
- einen je Mandant eindeutigen, normalisierten Code;
- Namen und IANA-Zeitzone;
- Aktiv-/Inaktiv-Status;
- Audit-Zeitpunkte;
- das Domain Event `SiteCreated`.

Die Datenbank erzwingt die Mandantenbeziehung und die Eindeutigkeit von
`(tenant_id, code)`. Inaktive Standorte dürfen nicht fachlich geändert, aber
wieder aktiviert werden.

## Migrationen

| Migration | Tabellen |
| --- | --- |
| `Version20260917094500` | `wms_tenant` |
| `Version20260917095500` | `wms_site` |

Die Tabellen sind bewusst von den Legacy-Tabellen getrennt.
