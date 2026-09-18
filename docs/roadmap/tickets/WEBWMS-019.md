---
id: WEBWMS-019
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Teilweise umgesetzt
priority: High
story_points: 5
component: "Lagerverwaltung"
feature_group: "Bestand"
source_feature: CG-019
---

# WEBWMS-019: Sonderbestandskennzeichen implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Sonderbestandskennzeichen“ nutzen, damit bestände nach Eigentum, Status oder Sonderart unterscheiden.

## Fachlicher Umfang

Bestände nach Eigentum, Status oder Sonderart unterscheiden.

**Prozesskontext:** Bestand → Kennzeichen

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Sonderbestandskennzeichen“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestände nach Eigentum, Status oder Sonderart unterscheiden.
3. Der Ablauf „Bestand → Kennzeichen“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockItem, SpecialStockType.
Vorgesehener Service: SpecialStockService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Bestandsstatus vorhanden; allgemeine Sonderbestandsarten fehlen. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-019
- Referenz: https://www.coglas.com/lagerverwaltung/

