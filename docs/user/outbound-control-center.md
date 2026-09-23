# Warenausgangsleitstand

Unter **Warenausgang → Warenausgangsleitstand** steuern Sie die übergreifenden Schritte von der Bedarfsvorschau bis zur Tour- und Dokumentenplanung. Die operativen Arbeitsplätze für Auftrag, Kommissionierung, Packen, Versand und Verladung bleiben direkt verlinkt.

## Vorschau und Auftragsprüfung

Die Bedarfsvorschau stellt offene Auftragsmengen dem verfügbaren Bestand gegenüber und markiert Engpässe je Artikel. Importierte Aufträge können geprüft, freigegeben oder vor der Freigabe mit Begründung storniert werden. Die Freigabe erzeugt positionsbezogene Reservierungen; Entnahmestrategien übernehmen die automatische Allokation.

## Ausgangs-QS und Packen

Eine vollständig gepickte Liste wird im Leitstand auf Vollständigkeit, Zustand und Kundenvorgaben geprüft. Sobald ein Prüfpunkt fehlschlägt, lautet die Entscheidung **gesperrt** und eine Notiz ist verpflichtend. Nur freigegebene Picklisten können in einen Packauftrag überführt werden.

Der Packarbeitsplatz führt durch Pickpositionen, Paketnummer und Gewicht. Der Abschluss vergleicht Pick- und Packmenge automatisch. Aktive Paketgewichtsgrenzen werden bereits beim Versiegeln geprüft.

## Versand, Label und Tracking

Versandregeln ordnen Gewichtsbereiche nach Priorität einem Carrier und Service zu. Carrier-Label, Trackingnummer und Druckauftrag werden über die vorhandenen Carrier- und Drucker-Gateways erzeugt. Weitere Trackingereignisse können mit Status, Ort, Beschreibung und Ereigniszeit gespeichert werden und stehen der Integrations-Outbox zur Rückmeldung an ERP oder Shop zur Verfügung.

## Touren, Verladung und Dokumente

Touren enthalten Carrier, Fahrzeug, Abfahrtszeit, maximale Last und eine geordnete Stoppliste. Lademanifeste prüfen jede Sendung beim Verladescan gegen Tour und Mandant. Erst vollständig gescannte Manifeste können abgeschlossen werden.

Lieferschein, Packliste, individuelle Ladeliste und CMR-Frachtbrief werden als unveränderliche HTML-Momentaufnahme mit Dokumentnummer und SHA-256-Prüfsumme archiviert. Sie können aus dem Dokumentenarchiv direkt geöffnet und gedruckt werden.

## API

`GET /api/v3/outbound/control` liefert Vorschau, QS, Regeln, Tracking, Dokumente, Touren und Gewichtsgrenzen. Schreiboperationen stehen unter folgenden Endpunkten bereit:

- `POST /api/v3/outbound/control/orders/{orderId}/cancel`
- `POST /api/v3/outbound/control/quality-checks`
- `POST /api/v3/outbound/control/shipping-rules`
- `GET /api/v3/outbound/control/shipping-rules/select/{weight}`
- `POST /api/v3/outbound/control/tracking-events`
- `POST /api/v3/outbound/control/documents`
- `POST /api/v3/outbound/control/tours`
- `POST /api/v3/outbound/control/weight-constraints`

Die bestehenden APIs für Aufträge, Picking, Packen, Versand, Carrier, Verladung und Outbox bilden die operativen Teilschritte ab.
