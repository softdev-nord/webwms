---
id: WEBWMS-041
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Umgesetzt
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Leitstand"
source_feature: CG-041
---

# WEBWMS-041: Kommissionierleitstand implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Kommissionierleitstand“ nutzen, damit offene, laufende und gestörte Pickaufträge überwachen.

## Fachlicher Umfang

Offene, laufende und gestörte Pickaufträge überwachen.

**Prozesskontext:** Planen → Zuweisen → Überwachen

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Kommissionierleitstand“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Offene, laufende und gestörte Pickaufträge überwachen.
3. Der Ablauf „Planen → Zuweisen → Überwachen“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: TaskBoard, PickOrder, UserAssignment.
Vorgesehener Service: PickingControlTowerService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Umgesetzt

Der mandantengetrennte Leitstand zeigt Picklisten, Aufträge, Wellen, Zielbehälter, Bearbeiter, Fortschritt und Fehlmengen und bietet die jeweils zulässigen operativen Aktionen an.

## Quelle

- Feature: CG-041
- Referenz: https://www.coglas.com/funktionen/
