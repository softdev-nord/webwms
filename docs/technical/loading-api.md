# Ladelisten und Verladung über API v3

Der Slice führt etikettierte Sendungen über Tour und Fahrzeug bis zur
kontrollierten Verladung und gemeinsamen Carrier-Übergabe. Er erweitert
`WEBWMS-060`, `WEBWMS-061`, `WEBWMS-063` und `WEBWMS-084`.

## Ablauf

1. `POST /api/v3/loading-manifests` erzeugt ein Manifest aus mindestens einer
   etikettierten Sendung sowie Tour- und Fahrzeugreferenz.
2. `GET /api/v3/loading-manifests/{id}` liefert Manifest, Ladeliste und
   Auditstatus jeder Sendung.
3. `POST /api/v3/loading-manifests/{id}/shipments/{shipmentId}/loading`
   bestätigt eine einzelne physische Verladung.
4. `POST /api/v3/loading-manifests/{id}/complete` prüft die Vollständigkeit,
   schließt das Manifest und überführt alle Sendungen atomar nach `dispatched`.

## Zustände und Konsistenz

Manifeststatus wechseln von `open` über `loading` nach `completed`. Eine
Sendung darf aufgrund eines eindeutigen Indexes nur einem Manifest angehören.
Nur `labelled`-Sendungen desselben Mandanten können eingeplant werden. Eine
Verladung kann nur einmal bestätigt werden. Der Abschluss verlangt, dass alle
Positionen `loaded` sind; der Manifestcode wird als Übergabereferenz der
Sendungen gespeichert.

Die Migration `Version20260917143000` enthält Manifest und Ladeliste bereits
vollständig. Dieser API-Slice benötigt keine Schemaänderung.

## Berechtigungen

- `fulfillment.loading.write`: Manifest anlegen;
- `fulfillment.loading.read`: Manifest und Ladeliste lesen;
- `fulfillment.loading.execute`: Verladung bestätigen und abschließen.

Mandant und Auditbenutzer stammen ausschließlich aus der authentifizierten
API-Identität. Die Persistenz prüft Benutzer, Manifest und Sendungen nochmals
innerhalb derselben Transaktion.

## Grenzen

Stoppreihenfolge, Kapazitäts- und Gewichtsrestriktionen, Gefahrguttrennung,
CMR-Frachtbrief, Druckausgabe und Telematik folgen in späteren Slices.
