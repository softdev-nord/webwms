---
id: WEBWMS-047
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Medium
story_points: 13
component: "Transport & Kommissionierung"
feature_group: "Materialfluss"
source_feature: WEBWMS-REQ-047
---

# WEBWMS-047: Materialflusssteuerung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Materialflusssteuerung“ nutzen, damit transporte zwischen Prozessstationen planen, auslösen und überwachen.

## Fachlicher Umfang

Transporte zwischen Prozessstationen planen, auslösen und überwachen.

**Prozesskontext:** Ereignis → Routing → Transport

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Materialflusssteuerung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Transporte zwischen Prozessstationen planen, auslösen und überwachen.
3. Der Ablauf „Ereignis → Routing → Transport“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MaterialFlowOrder, ProcessStation.
Vorgesehener Service: MaterialFlowService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Prozessstationen und Transportregeln bilden den Materialfluss zwischen Lager-, Pick-, Konsolidierungs-, Pack-, Versand- und Pufferstationen ab. Fahrbefehle werden im gemeinsamen Leitstand ausgelöst und überwacht.

## Quelle

- Feature: WEBWMS-REQ-047
