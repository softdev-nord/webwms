# Verladung, Touren und Manifeste

## Modell

Ein Lademanifest bündelt mindestens eine etikettierte Sendung für eine Tour und
ein Fahrzeug. Die Kombination aus Manifest und Sendungen bildet die
kontrollierbare Ladeliste.

| Manifeststatus | Bedeutung |
|---|---|
| `open` | Manifest angelegt, noch keine Sendung bestätigt |
| `loading` | Mindestens eine Sendung wurde verladen |
| `completed` | Alle Sendungen verladen und Carrier-Übergabe gebucht |

Jede Sendung darf durch einen eindeutigen Datenbankindex nur auf einem Manifest
stehen. Pro Position werden verladen durch und verladen am protokolliert.

## Transaktionen

Anlage, Verladebestätigung und Abschluss laufen transaktional und
mandantenbezogen. Die betroffenen Zeilen werden mit `FOR UPDATE` gesperrt.
Der Abschluss ist nur möglich, wenn alle Positionen `loaded` sind. Dann werden
alle enthaltenen Sendungen atomar von `labelled` nach `dispatched` überführt;
der Manifestcode dient als Übergabereferenz.

## Persistenz

- `wms_loading_manifest`: Tour, Fahrzeug, Status und Abschlussaudit;
- `wms_loading_manifest_shipment`: Ladeliste und Scan-Audit;
- Migration `Version20260917143000`.

## Grenzen

Routenoptimierung, Stoppreihenfolge, Kapazitätsberechnung, Gefahrguttrennung,
CMR-Frachtbrief, Druckausgabe und Telematik sind noch nicht enthalten.
