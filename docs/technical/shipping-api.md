# Versandprozess über API v3

Der Slice führt einen abgeschlossenen Packauftrag über Sendung, Label- und
Trackingregistrierung bis zur bestätigten Carrier-Übergabe. Er erweitert
`WEBWMS-056`, `WEBWMS-057`, `WEBWMS-058` und die API-Grundlage `WEBWMS-084`.

## Ablauf und Zustände

1. `POST /api/v3/packing-orders/{id}/shipments` erzeugt die Sendung mit
   Sendungsnummer, Carrier und Service im Status `prepared`.
2. `POST /api/v3/shipments/{id}/label` speichert Trackingnummer und externe
   Labelreferenz; der Status wechselt auf `labelled`.
3. `GET /api/v3/shipments/{id}` liefert den aktuellen Zustand einschließlich
   der Referenzen auf Pack-, Pick- und Kundenauftrag.
4. `POST /api/v3/shipments/{id}/dispatch` dokumentiert die physische
   Carrier-Übergabe und setzt `dispatched`.

Jeder Übergang sperrt die Sendung mit `SELECT ... FOR UPDATE`. Ein erneuter
oder übersprungener Übergang wird abgewiesen. Eine bereits einem Lademanifest
zugeordnete Sendung kann nicht direkt über den Versandendpunkt übergeben werden;
sie muss den separaten Verladeabschluss durchlaufen.

## Eindeutigkeit und Mandantentrennung

- pro abgeschlossenem Packauftrag höchstens eine Sendung;
- Sendungsnummer pro Mandant eindeutig;
- Trackingnummer pro Mandant und Carrier eindeutig;
- Packauftrag, Sendung und Auditbenutzer werden innerhalb des Mandanten
  geprüft.

Die Migration `Version20260917140000` enthält das vollständige Versandmodell;
dieser API-Slice benötigt keine Schemaänderung.

## Berechtigungen

- `fulfillment.ship.write`: Sendung erzeugen;
- `fulfillment.ship.read`: Sendung lesen;
- `fulfillment.ship.label`: Label und Tracking registrieren;
- `fulfillment.ship.dispatch`: Übergabe bestätigen.

Mandant und ausführender Benutzer stammen ausschließlich aus der
authentifizierten API-Identität.

## Grenzen

Die API registriert eine von einem Carrier gelieferte Labelreferenz, erzeugt
aber noch keine Labeldatei. Carrier-Kommunikation, Druckauftrag,
Mehrpaketsendung, Adressvalidierung, Zollinformationen und ausgehende
Status-Webhooks folgen in späteren Slices.
