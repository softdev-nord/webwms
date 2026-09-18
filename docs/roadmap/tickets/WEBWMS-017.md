---
id: WEBWMS-017
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Bestand"
source_feature: CG-017
---

# WEBWMS-017: Echtzeitbestand implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Echtzeitbestand“ nutzen, damit mengen, Status, Plätze und Bewegungen aktuell führen.

## Fachlicher Umfang

Mengen, Status, Plätze und Bewegungen aktuell führen.

**Prozesskontext:** Buchung → Bestand → Verfügbarkeit

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Echtzeitbestand“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Mengen, Status, Plätze und Bewegungen aktuell führen.
3. Der Ablauf „Buchung → Bestand → Verfügbarkeit“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockItem, StockBalance, StockMovement.
Vorgesehener Service: InventoryService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`wms_stock_balance`, `PostStockHandler`, atomare Buchungen. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-017
- Referenz: https://www.coglas.com/lagerverwaltung/

