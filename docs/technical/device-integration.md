# Scanner- und MDE-Integration

`WEBWMS-090` stellt einen mandantengebundenen Kern für Barcode-Scanner, MDEs
und mobile Browser bereit. Geräte besitzen einen eindeutigen Code, einen Typ und
einen pausierbaren Aktivstatus.

## Scanvertrag

Ein Scan enthält Gerät, Scantyp, Scanwert, Prozess, Prozessreferenz und eine
stabile `requestId`. Unterstützt werden Lagerplätze, Artikel, Chargen,
Seriennummern, Sendungen und Aufträge in Wareneingang, Kommissionierung, Packen,
Versand, Verladung und Inventur.

`(tenant_id, request_id)` ist eindeutig. Eine Wiederholung derselben Anfrage
liefert das bereits gespeicherte Ereignis zurück und erzeugt keinen doppelten
Scan. Nur aktive Geräte des authentifizierten Mandanten dürfen scannen.

Akzeptierte und abgelehnte Scans werden gleichermaßen mit Benutzer, Zeitpunkt
und optionalem Prüfhinweis protokolliert. Die fachliche Validierung eines
konkreten Prozesses entscheidet künftig vor dem Aufruf, welcher Status und
Hinweis gespeichert werden.

## API

- `GET|POST /api/v3/devices`
- `PATCH /api/v3/devices/{deviceId}/status`
- `GET|POST /api/v3/scan-events`

Die Rechte `integration.device.read`, `integration.device.write` und
`integration.device.scan` trennen Lesenzugriff, Verwaltung und Ausführung.
