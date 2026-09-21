---
id: WEBWMS-046
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Umgesetzt
priority: Medium
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Nachschub"
source_feature: CG-046
---

# WEBWMS-046: Vorholung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Vorholung“ nutzen, damit lagereinheiten vor Bedarfszeitpunkt in Zugriffszone bereitstellen.

## Fachlicher Umfang

Lagereinheiten vor Bedarfszeitpunkt in Zugriffszone bereitstellen.

**Prozesskontext:** Bedarfsvorschau → Vorholung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Vorholung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lagereinheiten vor Bedarfszeitpunkt in Zugriffszone bereitstellen.
3. Der Ablauf „Bedarfsvorschau → Vorholung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PrepositioningTask, HandlingUnit.
Vorgesehener Service: PrepositioningService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Umgesetzt

Vorholungen werden als priorisierte Fahrbefehle mit Fälligkeit, Quelle, Ziel und optionalem Artikelbestand geplant, disponiert, ausgeführt und nachvollziehbar abgeschlossen.

## Quelle

- Feature: CG-046
- Referenz: https://www.coglas.com/funktionen/
