---
id: WEBWMS-048
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Medium
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Materialfluss"
source_feature: WEBWMS-REQ-048
---

# WEBWMS-048: Routenzug implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Routenzug“ nutzen, damit feste oder dynamische Routenzugtouren für interne Transporte steuern.

## Fachlicher Umfang

Feste oder dynamische Routenzugtouren für interne Transporte steuern.

**Prozesskontext:** Bedarf → Tour → Übergabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Routenzug“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Feste oder dynamische Routenzugtouren für interne Transporte steuern.
3. Der Ablauf „Bedarf → Tour → Übergabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MilkRun, MilkRunStop, TransportTask.
Vorgesehener Service: MilkRunService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Feste oder dynamische Routenzüge besitzen geordnete Prozessstationen und optional ein Intervall. Eine Tourauslösung erzeugt für alle benachbarten Stopps ausführbare Fahrbefehle im Transportleitstand.

## Quelle

- Feature: WEBWMS-REQ-048
