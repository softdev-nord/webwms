---
id: WEBWMS-090
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Highest
story_points: 8
component: "Integration & Technik"
feature_group: "Hardware"
source_feature: WEBWMS-REQ-090
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

**Status:** Done

Mit `Device`, `ScanEvent`, `DeviceIntegrationService` und
`DbalDeviceRepository` sind mandantengebundene Geräteverwaltung, Aktivstatus,
idempotente Scanannahme und ein auditierbares Scanereignisjournal vorhanden.
API v3 und V3-Arbeitsbereich ermöglichen Anlage, Statuswechsel, Scanerfassung
sowie Detail- und Verlaufsansichten. Akzeptierte und abgelehnte Scans bilden
positive und negative Prüfpfade ab. Nachweise: `DeviceApiController`,
`DeviceController`, Migration `Version20260920100000`, Domain- und Query-Tests
sowie die technische und Anwenderdokumentation.

Der Scanvertrag validiert die Prozessarten Wareneingang, Picking, Packing,
Versand, Verladung und Inventur sowie die fachlichen Scanarten. Aktive Geräte,
Kontextreferenz, Request-ID und Annahme- oder Ablehnungsstatus werden vor der
Persistierung geprüft. API v3 dient zugleich als geräteunabhängiges Online-
Protokoll; MDE-spezifische Clients können denselben idempotenten Vertrag nutzen.
Damit ist der Ablauf Gerät → Scan → Prozess vollständig und nachvollziehbar.

## Quelle

- Feature: WEBWMS-REQ-090
