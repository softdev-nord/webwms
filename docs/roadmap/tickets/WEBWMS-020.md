---
id: WEBWMS-020
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Rückverfolgung"
source_feature: CG-020
---

# WEBWMS-020: Chargenverwaltung implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Chargenverwaltung“ nutzen, damit chargen aus Logistik und Produktion verwalten und rückverfolgen.

## Fachlicher Umfang

Chargen aus Logistik und Produktion verwalten und rückverfolgen.

**Prozesskontext:** Eingang → Charge → Ausgang

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Chargenverwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Chargen aus Logistik und Produktion verwalten und rückverfolgen.
3. Der Ablauf „Eingang → Charge → Ausgang“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Batch, StockItem, TraceabilityLink.
Vorgesehener Service: BatchService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Chargendimension in `StockDimensions`. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-020
- Referenz: https://www.coglas.com/funktionen/

