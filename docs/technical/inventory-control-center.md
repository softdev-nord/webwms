# Inventory Control Center

## Modulstruktur

Die Inventursteuerung liegt unter
`src/Warehouse/InventoryCount/Presentation` und trennt Web- und API-Eingänge.
Die Oberfläche spiegelt diese Fähigkeit unter
`templates/warehouse/inventory_count` wider. Bestandsführung, Sperrbestand und
Rückverfolgung sind als API-Präsentation unter
`src/Warehouse/Stock/Presentation/Api` zusammengeführt.

Interne Controller tragen keine Versionskennung. Die bestehenden `/v3`- und
`/api/v3`-Verträge sowie sämtliche Routennamen bleiben unverändert. Die
vorhandenen Anwendungsfälle in `src/Inventory/Application` werden zunächst
weiterverwendet und in einem späteren Application-Slice nach Fähigkeiten
zerlegt.

Der Slice WEBWMS-026 bis WEBWMS-032 schließt das Inventory-Epic mit einem gemeinsamen, mandantenfähigen Control Center ab.

## Bausteine

- Gefahrstoffklassen, Artikelklassifizierung und zulässige Lagerbereiche mit Höchstmengen
- versionierte Stücklisten, Komponenten und produktionsbezogene Materialbedarfe
- transaktionale LHM-Konten mit unveränderlichem Bewegungsjournal
- Stichtags-, permanente und automatisch erzeugte Nulldurchgangsinventuren
- Blindzählung, Differenzermittlung, Vier-Augen-Freigabe und Ledger-Korrekturbuchung

Web und REST verwenden dieselben Application Services und vorhandenen Inventur-Handler. Alle Referenzen werden auf Mandantenzugehörigkeit geprüft. Schreiboperationen für Stücklisten und LHM-Konten sind transaktional.

## Endpunkte

- `GET /api/v3/inventory/control`
- `POST /api/v3/inventory/control/hazard-classes`
- `POST /api/v3/inventory/control/hazardous-materials`
- `POST /api/v3/inventory/control/storage-restrictions`
- `POST /api/v3/inventory/control/boms`
- `POST /api/v3/inventory/control/requirements`
- `POST /api/v3/inventory/control/load-carrier-movements`
- `POST /api/v3/inventory/control/counts`
- `POST /api/v3/inventory/control/cycle-plans`
- `POST /api/v3/inventory/control/cycle-plans/{id}/start`
- `PUT /api/v3/inventory/control/counts/{id}/lines/{lineId}`
- `POST /api/v3/inventory/control/counts/{id}/submit`
- `POST /api/v3/inventory/control/counts/{id}/approve`

## Berechtigungen

Lesen erfolgt über `inventory.control.read`. Schreib- und Ausführungsrechte sind fachlich getrennt: `inventory.hazard.write`, `inventory.bom.write`, `inventory.bom.execute`, `inventory.load_carrier.write`, `inventory.count.write`, `inventory.count.execute` und `inventory.count.approve`.
