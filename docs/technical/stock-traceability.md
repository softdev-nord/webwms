# Bestandsattribute und Rückverfolgung

Der Slice WEBWMS-019 bis WEBWMS-022 ergänzt die Bestandsdimensionen um konfigurierbare Sonderbestandskennzeichen und stellt Charge, MHD und Seriennummer als operative V3-Rückverfolgung bereit.

## Sonderbestände

`wms_special_stock_type` definiert mandantenbezogene Kennzeichen der Kategorien Eigentum, Status und Sonderart. `wms_stock_classification` ordnet ein Kennzeichen exakt einer Bestandsposition aus Artikel, Lagerplatz und `stock_key` zu. Jede Änderung erzeugt zusätzlich ein unveränderliches Ereignis in `wms_stock_classification_event` mit Grund, Benutzer und Zeitpunkt.

Nicht allokierbare Sonderbestände werden aus der Bestandsauswahl im Warenausgang ausgeschlossen.

## Rückverfolgung

`ApiV3QueryService::traceability()` projiziert:

- Chargen mit Artikel, Menge, Lagerplatzanzahl und frühestem MHD,
- MHD-Bestände mit den Ampelstufen `expired`, `critical` und `ok`,
- Seriennummern mit aktuellem Lagerplatz und Status.

Der Lebenslauf einer Charge oder Seriennummer wird chronologisch aus dem unveränderlichen Bestandsledger aufgebaut. Abgelaufene Bestände werden nicht mehr zur Allokation angeboten. Seriennummern dürfen mandantenweit nur an einer positiven Bestandsposition geführt werden und behalten die Mengenregel eins.

## Endpunkte

- `GET /api/v3/inventory/traceability`
- `GET /api/v3/inventory/traceability/{batch|serial}/{value}`
- `GET|POST /api/v3/inventory/special-stock-types`
- `PUT /api/v3/inventory/stock-classifications`

Die Berechtigungen sind in `inventory.traceability.read` und `inventory.special_stock.read|write` getrennt.
