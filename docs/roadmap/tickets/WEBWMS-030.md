---
id: WEBWMS-030
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Offen
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

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-030
- Referenz: https://www.coglas.com/funktionen/

