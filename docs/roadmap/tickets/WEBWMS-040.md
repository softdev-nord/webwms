---
id: WEBWMS-040
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Optimierung"
source_feature: WEBWMS-REQ-040
---

# WEBWMS-040: Wegeoptimierung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Wegeoptimierung“ nutzen, damit pickreihenfolge anhand Lagertopologie und Regeln optimieren.

## Fachlicher Umfang

Pickreihenfolge anhand Lagertopologie und Regeln optimieren.

**Prozesskontext:** Tasks → Route → Sequenz

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Wegeoptimierung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Pickreihenfolge anhand Lagertopologie und Regeln optimieren.
3. Der Ablauf „Tasks → Route → Sequenz“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: RouteGraph, PickSequence.
Vorgesehener Service: RouteOptimizationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Offene Pickpositionen werden transaktional nach Lagerbereich, Gang, Ebene und Fach neu sequenziert. Die Optimierung ist in Pickansicht und API v3 verfügbar und wird im Fulfillment-Journal protokolliert.

## Quelle

- Feature: WEBWMS-REQ-040
