---
id: WEBWMS-090
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Integration & Technik"
feature_group: "Hardware"
source_feature: CG-090
---

# WEBWMS-090: Scanner und MDE implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Scanner und MDE“ nutzen, damit barcode-Scanner und mobile Datenerfassungsgeräte prozessintegriert nutzen.

## Fachlicher Umfang

Barcode-Scanner und mobile Datenerfassungsgeräte prozessintegriert nutzen.

**Prozesskontext:** Gerät → Scan → Prozess

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Scanner und MDE“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Barcode-Scanner und mobile Datenerfassungsgeräte prozessintegriert nutzen.
3. Der Ablauf „Gerät → Scan → Prozess“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Device, ScanEvent.
Vorgesehener Service: DeviceIntegrationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `Device`, `ScanEvent`, `DeviceIntegrationService` und
`DbalDeviceRepository` sind mandantengebundene Geräteverwaltung, Aktivstatus,
idempotente Scanannahme und ein auditierbares Scanereignisjournal vorhanden.
API v3 und V3-Arbeitsbereich ermöglichen Anlage, Statuswechsel, Scanerfassung
sowie Detail- und Verlaufsansichten. Akzeptierte und abgelehnte Scans bilden
positive und negative Prüfpfade ab. Nachweise: `DeviceApiController`,
`V3DeviceController`, Migration `Version20260920100000`, Domain- und Query-Tests
sowie die technische und Anwenderdokumentation.

Die direkte Ausführung und Validierung konkreter Wareneingangs-, Pick-, Pack-,
Versand-, Verlade- und Inventuraktionen, Offline-Fähigkeit sowie vollständige
HTTP-/MariaDB-Integrationstests fehlen noch; das Ticket ist deshalb nicht
`Done`.

## Quelle

- Feature: CG-090
- Referenz: https://www.coglas.com/hardware-schnittstelle/
