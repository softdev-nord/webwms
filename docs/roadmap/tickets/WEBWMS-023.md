---
id: WEBWMS-023
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Strategien"
source_feature: CG-023
---

# WEBWMS-023: FIFO/LIFO implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „FIFO/LIFO“ nutzen, damit auslagerung nach Buchungs- oder Zugangsreihenfolge steuern.

## Fachlicher Umfang

Auslagerung nach Buchungs- oder Zugangsreihenfolge steuern.

**Prozesskontext:** Bedarf → Bestandsselektion

## Akzeptanzkriterien

1. Berechtigte Benutzer können „FIFO/LIFO“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Auslagerung nach Buchungs- oder Zugangsreihenfolge steuern.
3. Der Ablauf „Bedarf → Bestandsselektion“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockSelectionRule, StockItem.
Vorgesehener Service: StockAllocationStrategy.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Strategiegrundlagen vorhanden; allgemeines FIFO/LIFO fehlt. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-023
- Referenz: https://www.coglas.com/lagerverwaltung/

