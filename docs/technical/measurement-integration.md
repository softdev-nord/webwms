# Waagen- und Volumenmessungsintegration

Der Slice `WEBWMS-092` bindet Waagen, Dimensioner und Kombigeräte mandantenfähig an die API v3 an. `MeasurementDevice` beschreibt die Gerätefähigkeit; `Measurement` bildet ein unveränderliches, über `tenant_id` und `request_id` idempotentes Messereignis ab.

## Verarbeitung

`MeasurementService` prüft ein aktives Gerät und die Übereinstimmung von Gerätetyp und Messwerten. Waagen liefern Gewicht, Dimensioner vollständige Länge/Breite/Höhe und Kombigeräte beide Wertgruppen. `DbalMeasurementRepository` speichert akzeptierte oder abgelehnte Messungen transaktional. Akzeptierte Messungen aktualisieren:

- `wms_package` innerhalb eines offenen Packauftrags;
- `wms_product_reference` innerhalb desselben Mandanten.

Abgelehnte Messungen bleiben im Journal, verändern das Ziel jedoch nicht. Die polymorphe Zielreferenz wird vor jeder Übernahme mandantensicher aufgelöst.

## Schnittstellen

- `GET/POST /api/v3/measurement-devices`
- `PATCH /api/v3/measurement-devices/{deviceId}/status`
- `GET/POST /api/v3/measurements`
- V3-Arbeitsbereich `/v3/integration/measurements`

Die Rechte `integration.measurement.read`, `integration.measurement.write` und `integration.measurement.capture` trennen Lesenzugriff, Geräteverwaltung und Messwerterfassung.

## Persistenz

Migration `Version20260920120000` ergänzt `wms_measurement_device`, `wms_measurement`, Paketabmessungen sowie Gewicht und Abmessungen an der Produktreferenz. Eindeutige Request-IDs verhindern doppelte Ereignisse. Geräte, Benutzer und Journal sind über Tenant- und Audit-Referenzen abgesichert.
