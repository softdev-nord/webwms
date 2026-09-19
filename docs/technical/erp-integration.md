# Generische ERP-Integration

Der Slice setzt den Backendkern von `WEBWMS-087` auf dem vorhandenen API-v3-
und Outbox-Fundament um. Ein ERP übernimmt Stamm- und Auftragsdaten über die
JSON-API, liest Bestände und Bewegungen und empfängt Prozessstatus automatisch
als signierte HTTP-Ereignisse.

## Verbindungsmodell

Migration `Version20260919140000` erzeugt `wms_erp_connection` mit:

- Mandant, eindeutiger Name und HTTPS-Basis-URL;
- Referenz auf eine Umgebungsvariable mit dem Signaturschlüssel;
- Aktivstatus;
- Ersteller und Erstellungszeitpunkt;
- Benutzer und Zeitpunkt der letzten Statusänderung.

Secrets werden weder in der Datenbank noch in API-Antworten gespeichert. Der
`EnvironmentCredentialProvider` löst ausschließlich den konfigurierten Namen
zur Laufzeit auf. Fehlt die Variable, schlägt die Zustellung kontrolliert fehl
und Symfony Messenger übernimmt Retry und Failure Queue.

## Datenfluss

Eingehende Daten verwenden die vorhandenen mandantengebundenen Ressourcen:

| ERP-Daten | API-v3-Ressource |
| --- | --- |
| Artikelstamm | `POST /api/v3/products` |
| Kundenauftrag | `POST /api/v3/orders` |
| Echtzeitbestand | `GET /api/v3/stock` |
| Bewegungsjournal | `GET /api/v3/stock-movements` |

Ausgehende `PublishedIntegrationMessage` werden vom
`DeliverErpStatusEventHandler` verarbeitet. Er lädt ausschließlich aktive
Verbindungen desselben Mandanten und delegiert jede Zustellung an
`HttpErpStatusTransport`.

## Status-Webhook

Der Transport sendet an `{endpointUrl}/status-events`:

```json
{
  "id": "OUTBOX_UUID",
  "eventName": "fulfillment.shipment.dispatched",
  "aggregateType": "shipment",
  "aggregateId": "SHIPMENT_UUID",
  "occurredAt": "2026-09-19T14:00:00+00:00",
  "payload": {"status": "dispatched"}
}
```

Header:

- `Idempotency-Key`: unveränderte Outbox-Nachrichten-ID;
- `X-WebWMS-Event`: Ereignisname;
- `X-WebWMS-Signature`: `sha256=` plus HMAC-SHA-256 des exakten Request-Bodys;
- `Content-Type: application/json`.

Nur HTTP 2xx gilt als Erfolg. Timeout, fehlendes Secret und andere Statuscodes
werfen eine Exception und aktivieren die Messenger-Retry-Strategie. Da mehrere
Verbindungen nacheinander bedient werden, müssen Empfänger die
`Idempotency-Key`-Semantik einhalten.

## Verwaltungs-API

| Methode | Pfad | Berechtigung |
| --- | --- | --- |
| `GET` | `/api/v3/erp-connections` | `integration.erp_connection.read` |
| `POST` | `/api/v3/erp-connections` | `integration.erp_connection.write` |
| `PATCH` | `/api/v3/erp-connections/{id}/status` | `integration.erp_connection.write` |

URLs müssen HTTPS verwenden. Credential-Referenzen folgen
`^[A-Z][A-Z0-9_]{2,100}$`. Alle Lese- und Schreibzugriffe sind an den Mandanten
der authentifizierten Identität gebunden.

## Betriebsgrenzen

Ausgehende Netzwerkziele sollten zusätzlich durch die Infrastruktur-Egress-
Policy eingeschränkt werden. Konkrete SAP-, Microsoft-Dynamics- oder andere
herstellerspezifische Mappings gehören in nachfolgende Adapter. Die generische
Verbindung überträgt bewusst den stabilen WebWMS-Ereignisvertrag.
