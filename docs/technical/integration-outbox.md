# Statusrückmeldung und Integrations-Outbox

Der Slice setzt `WEBWMS-065` als transaktionale Statusrückmeldung um und legt
für `WEBWMS-096` die Outbox- und Idempotenzgrundlage.

## Transaktionsmodell

`DbalInventoryRepository` schreibt eine `IntegrationStatusEvent` über das
abstrahierte `OutboxRepository`. Fachänderung und Outbox-Eintrag verwenden
dieselbe Doctrine-DBAL-Verbindung und dieselbe Transaktion. Schlägt eine der
beiden Schreiboperationen fehl, werden beide zurückgerollt.

Migration `Version20260919100000` erzeugt `wms_integration_outbox` mit:

- Mandant, Ereignisname und Aggregatreferenz;
- JSON-Payload und fachlichem Ereigniszeitpunkt;
- Ersteller als Auditbenutzer;
- Zustand `pending` oder `acknowledged`;
- Quittierungsbenutzer und -zeitpunkt;
- Indizes für Pending-Abfrage und Aggregathistorie.

## Ereignisse

| Ereignis | Aggregat | Auslöser |
| --- | --- | --- |
| `fulfillment.pick.updated` | `pick_list` | Pick oder Fehlmenge bestätigt |
| `fulfillment.packing.completed` | `packing_order` | Packauftrag vollständig abgeschlossen |
| `fulfillment.shipment.labelled` | `shipment` | Tracking und Labelreferenz registriert |
| `fulfillment.shipment.dispatched` | `shipment` | Direkte oder manifestbasierte Übergabe |
| `fulfillment.loading.completed` | `loading_manifest` | Lademanifest vollständig abgeschlossen |

Beim Manifestabschluss wird neben dem Manifestereignis je Sendung ein eigenes
Dispatch-Ereignis geschrieben.

## Consumer-Protokoll

`GET /api/v3/outbox` liefert ausschließlich offene Nachrichten aufsteigend
nach UUIDv7. `limit` ist auf 1 bis 100 begrenzt; `meta.nextCursor` ermöglicht
seitenweises Lesen. Nach erfolgreicher Verarbeitung quittiert der Consumer mit
`POST /api/v3/outbox/{id}/acknowledgement`.

Die Quittierung ist idempotent: Eine bereits quittierte Nachricht kann erneut
bestätigt werden, ohne ihren ursprünglichen Auditzeitpunkt zu überschreiben.
Ohne Quittierung bleibt die Nachricht sichtbar. Dadurch entsteht eine
At-least-once-Zustellung; Zielsysteme müssen die Nachrichten-ID als
Idempotenzschlüssel verwenden.

## Sicherheit

- `integration.outbox.read`: offene Nachrichten des eigenen Mandanten lesen;
- `integration.outbox.acknowledge`: Verarbeitung quittieren.

Mandant und Quittierungsbenutzer stammen ausschließlich aus der
authentifizierten API-Identität.

## Grenzen

Der aktuelle Pull-Consumer übernimmt Wiederholungen durch erneutes Lesen.
Automatischer Queue-Push, Zustellversuche, exponentielles Backoff,
Dead-Letter-Status und Betriebsmetriken folgen im weiteren Ausbau von
`WEBWMS-096`.
