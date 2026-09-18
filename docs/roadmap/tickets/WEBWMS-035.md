---
id: WEBWMS-035
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Offen
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Kommissionierung"
source_feature: CG-035
---

# WEBWMS-035: Multi-Order-Picking implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Multi-Order-Picking“ nutzen, damit mehrere Aufträge in einem Rundgang mit Zielbehältern bearbeiten.

## Fachlicher Umfang

Mehrere Aufträge in einem Rundgang mit Zielbehältern bearbeiten.

**Prozesskontext:** Aufträge → Bündel → Sortierung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Multi-Order-Picking“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Mehrere Aufträge in einem Rundgang mit Zielbehältern bearbeiten.
3. Der Ablauf „Aufträge → Bündel → Sortierung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PickWave, PickCart, PickContainer.
Vorgesehener Service: MultiOrderPickingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-035
- Referenz: https://www.coglas.com/kommissionierung/

