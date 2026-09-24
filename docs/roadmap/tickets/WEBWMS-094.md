---
id: WEBWMS-094
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Medium
story_points: 13
component: "Integration & Technik"
feature_group: "Lagertechnik"
source_feature: CG-094
---

# WEBWMS-094: WCS/MFR/Fördertechnik implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „WCS/MFR/Fördertechnik“ nutzen, damit warehouse Control System, Materialflussrechner und Fördertechnik koppeln.

## Fachlicher Umfang

Warehouse Control System, Materialflussrechner und Fördertechnik koppeln.

**Prozesskontext:** WMS ↔ WCS/MFR

## Akzeptanzkriterien

1. Berechtigte Benutzer können „WCS/MFR/Fördertechnik“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Warehouse Control System, Materialflussrechner und Fördertechnik koppeln.
3. Der Ablauf „WMS ↔ WCS/MFR“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MachineCommand, MachineStatus.
Vorgesehener Service: WcsIntegrationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `WcsConnection`, `MachineCommand`, `MachineStatus` und `WcsIntegrationService` ist der mandantenfähige Kern für WCS, Materialflussrechner und Fördertechnik vorhanden. Migration und DBAL-Repository speichern Verbindungen, idempotente Transportbefehle, Maschinenzustände und Auditdaten. V3-Weboberfläche und JSON-API bilden den bidirektionalen Ablauf berechtigt ab; Demo-Daten sowie automatisierte Domain-, Application- und Query-Tests decken den Slice ab.

Zur vollständigen Umsetzung fehlen herstellerspezifische Protokolladapter, die asynchrone Zustellung über die Integrations-Outbox sowie End-to-End-Tests mit einem WCS-Simulator.

## Quelle

- Feature: CG-094
- Referenz: https://www.coglas.com/hardware-schnittstelle/
