---
id: WEBWMS-037
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Medium
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Kommissionierung"
source_feature: CG-037
---

# WEBWMS-037: Mehrstufige Kommissionierung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Mehrstufige Kommissionierung“ nutzen, damit pick, Konsolidierung und Packen über mehrere Stufen führen.

## Fachlicher Umfang

Pick, Konsolidierung und Packen über mehrere Stufen führen.

**Prozesskontext:** Pick → Konsolidierung → Packen

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Mehrstufige Kommissionierung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Pick, Konsolidierung und Packen über mehrere Stufen führen.
3. Der Ablauf „Pick → Konsolidierung → Packen“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PickStage, ConsolidationUnit.
Vorgesehener Service: MultiStagePickingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Zweistufige Pickwellen führen Picklisten über getrennte Zielbehälter in die Konsolidierung. Nur vollständig gepickte Listen einer freigegebenen Welle können konsolidiert werden; nach dem letzten Behälter wird die Welle abgeschlossen.

## Quelle

- Feature: CG-037
- Referenz: https://www.coglas.com/kommissionierung/
