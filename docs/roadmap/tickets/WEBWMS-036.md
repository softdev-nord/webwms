---
id: WEBWMS-036
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Umgesetzt
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Kommissionierung"
source_feature: CG-036
---

# WEBWMS-036: Wellenkommissionierung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Wellenkommissionierung“ nutzen, damit aufträge nach Zeit, Tour, Carrier oder Priorität bündeln.

## Fachlicher Umfang

Aufträge nach Zeit, Tour, Carrier oder Priorität bündeln.

**Prozesskontext:** Aufträge → Welle → Freigabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Wellenkommissionierung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Aufträge nach Zeit, Tour, Carrier oder Priorität bündeln.
3. Der Ablauf „Aufträge → Welle → Freigabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PickWave, WaveRule.
Vorgesehener Service: WavePlanningService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Umgesetzt

Pickwellen lassen sich nach Zeit, Tour, Carrier oder Priorität selektieren, planen und kontrolliert freigeben. Der Leitstand stellt Bearbeitungs- und Konsolidierungsfortschritt dar.

## Quelle

- Feature: CG-036
- Referenz: https://www.coglas.com/kommissionierung/
