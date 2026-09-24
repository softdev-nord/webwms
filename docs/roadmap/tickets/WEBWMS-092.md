---
id: WEBWMS-092
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Medium
story_points: 8
component: "Integration & Technik"
feature_group: "Hardware"
source_feature: WEBWMS-REQ-092
---

# WEBWMS-092: Waagen und Volumenmessung implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Waagen und Volumenmessung“ nutzen, damit gewicht und Abmessungen automatisch übernehmen.

## Fachlicher Umfang

Gewicht und Abmessungen automatisch übernehmen.

**Prozesskontext:** Messung → Paket/Artikel

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Waagen und Volumenmessung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Gewicht und Abmessungen automatisch übernehmen.
3. Der Ablauf „Messung → Paket/Artikel“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MeasurementDevice, Measurement.
Vorgesehener Service: MeasurementGateway.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `MeasurementDevice`, `Measurement`, `MeasurementService` und
`DbalMeasurementRepository` sind mandantengebundene Waagen, Dimensioner und
Kombigeräte sowie eine idempotente, auditierbare Messwertübernahme vorhanden.
Akzeptierte Messungen aktualisieren Paket oder Artikel transaktional; abgelehnte
Messungen bleiben ohne Zieländerung im Journal. API v3 und V3-Arbeitsbereich
decken Geräteanlage, Statuswechsel, Erfassung und Verlauf ab. Nachweise:
`MeasurementApiController`, `V3MeasurementController`, Migration
`Version20260920120000`, Domain-/Application-/Query-Tests sowie technische und
Anwenderdokumentation.

Reale Hardwareprotokolle und Treiber, automatische Push-Übertragung vom Gerät
sowie vollständige HTTP-/MariaDB-Integrationstests fehlen noch; das Ticket ist
deshalb nicht `Done`.

## Quelle

- Feature: WEBWMS-REQ-092
