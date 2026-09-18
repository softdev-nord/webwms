---
id: WEBWMS-030
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: High
story_points: 8
component: "Lagerverwaltung"
feature_group: "Inventur"
source_feature: CG-030
---

# WEBWMS-030: Permanente Inventur implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Permanente Inventur“ nutzen, damit zählungen über das Geschäftsjahr verteilen.

## Fachlicher Umfang

Zählungen über das Geschäftsjahr verteilen.

**Prozesskontext:** Planung → zyklische Zählung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Permanente Inventur“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Zählungen über das Geschäftsjahr verteilen.
3. Der Ablauf „Planung → zyklische Zählung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: CycleCountPlan, InventoryCount.
Vorgesehener Service: CycleCountingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Umgesetzt sind zyklische Inventurpläne mit Lagerbereich, Intervall und Fälligkeit sowie die transaktionale Erzeugung regulärer Inventurbelege aus fälligen Plänen. Nach der Ausführung wird der nächste Termin fortgeschrieben; parallele offene Zählungen desselben Bereichs werden verhindert.

Nachweise: `CycleCountPlan`, `CycleCountExecution`, die Application-Handler `CreateCycleCountPlanHandler` und `StartDueCycleCountHandler`, `DbalInventoryRepository` sowie Migration `Version20260918160000`.

API/UI, ticketbezogene Autorisierung und Datenbank-Integrationstests sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-030
- Referenz: https://www.coglas.com/funktionen/
