# Statusrückmeldung und Integrations-Outbox

Der Slice setzt `WEBWMS-065` als transaktionale Statusrückmeldung um und baut
`WEBWMS-096` zu einer asynchronen Veröffentlichung mit Wiederholungs- und
Dead-Letter-Verarbeitung aus.

## Transaktionsmodell

`DbalInventoryRepository` schreibt eine `IntegrationStatusEvent` über das
abstrahierte `OutboxRepository`. Fachänderung und Outbox-Eintrag verwenden
dieselbe Doctrine-DBAL-Verbindung und dieselbe Transaktion. Schlägt eine der
beiden Schreiboperationen fehl, werden beide zurückgerollt.

Migration `Version20260919100000` erzeugt `wms_integration_outbox`. Migration
`Version20260919120000` ergänzt Zustellzähler, Fälligkeit, Claim-Lease,
Veröffentlichungszeitpunkt, letzten Fehler und die Auditdaten einer manuellen
Wiederaufnahme. Die Zustände sind `pending`, `processing`, `published`,
`dead_letter` und `acknowledged`.

`wms_integration_attempt` hält jeden abgeschlossenen Queue-Versuch mit Nummer,
Ergebnis, Fehler und Zeitpunkt unveränderlich fest. `messenger_messages` ist
die persistente Doctrine-Queue für die Transportnamen `integration` und
`failed`.

## Ereignisse

| Ereignis | Aggregat | Auslöser |
| --- | --- | --- |
| `fulfillment.pick.updated` | `pick_list` | Pick oder Fehlmenge bestätigt |
| `fulfillment.packing.completed` | `packing_order` | Packauftrag vollständig abgeschlossen |
| `fulfillment.shipment.labelled` | `shipment` | Tracking und Labelreferenz registriert |
| `fulfillment.shipment.dispatched` | `shipment` | Direkte oder manifestbasierte Übergabe |
| `fulfillment.loading.completed` | `loading_manifest` | Lademanifest vollständig abgeschlossen |

## Automatischer Publisher

`OutboxPublisher` beansprucht bis zu 1.000 fällige Nachrichten in einer
DBAL-Transaktion. `SELECT ... FOR UPDATE` und der sofortige Wechsel nach
`processing` verhindern parallele Doppel-Claims. Ein fünf Minuten alter Claim
gilt als verwaist und darf erneut übernommen werden.

`MessengerOutboxTransport` übergibt eine `PublishedIntegrationMessage` an den
Symfony-Messenger-Transport `integration`. Die UUID der Outbox bleibt als
`messageId` erhalten. Erst nach erfolgreicher Queue-Übergabe wechselt der
Outbox-Datensatz nach `published`.

Der Publisher wird regelmäßig, beispielsweise minütlich, gestartet:

```bash
php bin/console webwms:outbox:publish --limit=100
```

Ein unabhängiger Worker verarbeitet die Queue. Der generische ERP-Handler
liefert Statusmeldungen an alle aktiven Verbindungen des Mandanten:

```bash
php bin/console messenger:consume integration --time-limit=3600
```

Queue-Übergabe und Outbox-Abschluss sind getrennte Transaktionen. Ein Absturz
dazwischen kann dieselbe `messageId` erneut in die Queue stellen. Nachgelagerte
Adapter müssen deshalb idempotent arbeiten.

## Retry und Dead Letter

Fehler bei der Queue-Übergabe werden mit exponentiellem Backoff nach 30, 60,
120 und 240 Sekunden erneut versucht. Der fünfte Fehlschlag setzt
`dead_letter`. Fehlermeldungen werden auf 1.000 Zeichen begrenzt.

Dead Letters können mandantenbezogen gelesen und mit
`POST /api/v3/outbox/{id}/retry` wieder auf `pending` gesetzt werden. Benutzer
und Zeitpunkt werden gespeichert; ein neuer Fünf-Versuche-Zyklus beginnt. Ein
Retry eines anderen Zustands wird als Konflikt abgelehnt.

## API und Sicherheit

`GET /api/v3/outbox` liefert standardmäßig offene Nachrichten aufsteigend nach
UUIDv7. Über `status` können auch `processing`, `published`, `dead_letter` oder
`acknowledged` abgefragt werden. `limit` ist auf 1 bis 100 begrenzt und
`meta.nextCursor` ermöglicht seitenweises Lesen. Die Pull-Kompatibilität über
`POST /api/v3/outbox/{id}/acknowledgement` bleibt erhalten.

- `integration.outbox.read`: Nachrichten des eigenen Mandanten lesen;
- `integration.outbox.acknowledge`: Pull-Verarbeitung quittieren;
- `integration.outbox.retry`: Dead Letter manuell wiederaufnehmen.

Mandant und ausführender Benutzer stammen ausschließlich aus der
authentifizierten API-Identität.

Das serverseitige V3-Frontend stellt dieselben tenantbezogenen Operationen
unter `/v3/integration/outbox` bereit. Berechtigte Benutzer können Nachrichten
nach Status filtern, Payload und Zustellfehler prüfen, offene Pull-Nachrichten
quittieren und Dead Letters nach Behebung der Ursache erneut einreihen.

## Grenzen

Der Slice stellt generische Ereignisse zuverlässig in die interne Queue und
liefert sie über den ERP-HTTP-Adapter aus. Herstellerspezifische ERP-Mappings,
weitere Zieladapter, aggregierte Betriebsmetriken und Alarmierung folgen.
Messenger besitzt zusätzlich seine eigene Retry-/Failure-Queue für Fehler, die
erst im Zieladapter auftreten.
