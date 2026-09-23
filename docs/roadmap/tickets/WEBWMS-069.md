---
id: WEBWMS-069
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: High
story_points: 13
component: "Zusatzfunktionen"
feature_group: "Automatisierung"
source_feature: CG-069
---

# WEBWMS-069: Eventcenter implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Eventcenter“ nutzen, damit ereignisbasierte Workflows wie Druck, Nachricht oder Folgeauftrag auslösen.

## Fachlicher Umfang

Ereignisbasierte Workflows wie Druck, Nachricht oder Folgeauftrag auslösen.

**Prozesskontext:** Domain Event → Regel → Aktion

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Eventcenter“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Ereignisbasierte Workflows wie Druck, Nachricht oder Folgeauftrag auslösen.
3. Der Ablauf „Domain Event → Regel → Aktion“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: AutomationRule, AutomationAction, DomainEvent.
Vorgesehener Service: WorkflowAutomationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Das Eventcenter verwaltet Eventname, JSON-Bedingungen und Aktionen für Druck, Nachricht oder Folgeauftrag. Passende Regeln erzeugen persistente, auditierbare Ausführungen; Testevents stehen in UI und API bereit.

Nachweise: `wms_automation_rule`, `wms_automation_execution`, `PlatformControlService::executeEvent()` und Platform Control Center.

## Quelle

- Feature: CG-069
- Referenz: https://www.coglas.com/funktionen/
