# Packprozess über API v3

Der Slice führt eine abgeschlossene Pickliste über Packauftrag und Packstücke
bis zum geprüften Packabschluss. Er erweitert `WEBWMS-053`, `WEBWMS-054` und
die API-Grundlage `WEBWMS-084`.

## Ablauf

1. `POST /api/v3/pick-lists/{id}/packing-orders` erzeugt aus einer
   abgeschlossenen Pickliste einen Packauftrag im Status `open`.
2. `POST /api/v3/packing-orders/{id}/packages` ordnet erfolgreich gepickte
   Positionen einem versiegelten Packstück zu und setzt den Auftrag auf
   `packing`.
3. `GET /api/v3/packing-orders/{id}` liefert Auftrags-, Packstück- und
   Positionsdaten.
4. `POST /api/v3/packing-orders/{id}/complete` vergleicht alle gepickten mit
   allen verpackten Positionen und schließt den Auftrag atomar ab.

## Konsistenz

`wms_packing_order.pick_list_id` ist eindeutig. Eine Pickliste kann daher nur
einen Packauftrag erzeugen. `wms_package_item.pick_task_id` verhindert, dass
eine Pickposition in mehrere Packstücke gelangt. Der Abschluss ist nur möglich,
wenn jede Position mit Status `picked` genau einmal in einem versiegelten
Packstück liegt. Positionen mit Fehlmenge werden nicht erwartet.

Die vorhandene Migration `Version20260917133000` deckt das Modell vollständig
ab; für diesen API-Slice ist keine Schemaänderung erforderlich.

## Sicherheit und Audit

- `fulfillment.pack.write`: Packauftrag und Packstücke anlegen;
- `fulfillment.pack.read`: Packauftrag lesen;
- `fulfillment.pack.execute`: Packauftrag abschließen.

Mandant und ausführender Benutzer stammen ausschließlich aus dem
authentifizierten API-Client. Repository-Prüfungen sichern Pickliste,
Packauftrag, Positionen und Benutzer erneut innerhalb des Mandanten ab.

## Grenzen

Abmessungen, Verpackungsmittelbestand, Umpacken, Etikettendruck,
Gefahrgutprüfung und eine grafische Packplatzführung folgen in späteren
Slices.
