---
id: WEBWMS-029
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Inventur"
source_feature: CG-029
---

# WEBWMS-029: Stichtagsinventur implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Stichtagsinventur“ nutzen, damit bestände zu einem Stichtag sperren, zählen und differenzbuchen.

## Fachlicher Umfang

Bestände zu einem Stichtag sperren, zählen und differenzbuchen.

**Prozesskontext:** Planung → Zählung → Differenz

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Stichtagsinventur“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestände zu einem Stichtag sperren, zählen und differenzbuchen.
3. Der Ablauf „Planung → Zählung → Differenz“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: InventoryCount, CountLine, Adjustment.
Vorgesehener Service: StocktakeService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`InventoryCountPlan`, `CreateInventoryCountHandler`, `RecordInventoryCountHandler`, `wms_inventory_count` und `wms_inventory_count_line` bilden Inventurplanung, Stichtags-Snapshot und Blindzählung ab. Migration `Version20260918120000`, Domain-Tests sowie technische und fachliche Dokumentation sind vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-029
- Referenz: https://www.coglas.com/funktionen/
