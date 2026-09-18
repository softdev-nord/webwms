# Picklisten und Pickaufträge über API v3

Der Slice führt den Kundenauftrag nach Reservierung und Allokation bis zur
operativen Kommissionierung weiter. Er vertikalisiert `WEBWMS-033` und
`WEBWMS-034`.

## Ablauf

1. `POST /api/v3/orders/{id}/pick-lists` prüft die vollständige Allokation,
   ermittelt alle aktiven Allokationen des freigegebenen Auftrags und erzeugt
   eine sequenzierte Pickliste.
2. `POST /api/v3/pick-lists/{id}/assignment` weist die Liste einem aktiven
   Benutzer desselben Mandanten zu.
3. `GET /api/v3/pick-lists/{id}` liefert Auftragsbezug, Status, Zuweisung und
   dimensionsgenaue Pickpositionen.
4. `POST /api/v3/pick-tasks/{id}/confirmation` bestätigt eine Position als
   `picked` oder `shortage`.

## Konsistenz und Zustände

`PickList` trägt eine `outboundOrderId`. Die Persistenz akzeptiert nur aktive
Allokationen, deren Reservierung zu Positionen dieses Auftrags gehört. Der
eindeutige Index auf `wms_pick_list.outbound_order_id` verhindert mehrere
Single-Order-Picklisten für denselben Auftrag.

Bei `picked` wird die Allokation atomar verbraucht und eine Ledger-ID
serverseitig erzeugt. Bei `shortage` wird die Allokation freigegeben. Erst wenn
keine offene Position verbleibt, wechselt die Liste auf `completed`.

## Sicherheit

- `fulfillment.pick.write`: Pickliste erzeugen;
- `fulfillment.pick.read`: Pickliste lesen;
- `fulfillment.pick.assign`: Benutzer zuweisen;
- `fulfillment.pick.execute`: Position bestätigen.

Mandant, Ersteller, Zuweisender und bestätigender Benutzer stammen aus der
authentifizierten Identität. Eine Bestätigung gelingt ausschließlich für eine
offene Position, deren Pickliste diesem Benutzer zugewiesen ist.

## Persistenz und Migration

Migration `Version20260918200000` ergänzt die optionale Fremdschlüsselspalte
`outbound_order_id`. Sie bleibt für historische Datensätze nullable; neue
Picklisten aus dem WebWMS-3.0-Kern besitzen immer einen Auftragsbezug.

## Grenzen

Wegeoptimierung, Teilmengen, Scanner-Validierung, Multi-Order- und
Wellenkommissionierung sind nicht Bestandteil dieses Slices. Eine Fehlmenge
gibt weiterhin die komplette Allokation frei.
