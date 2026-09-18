---
id: WEBWMS-031
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: Medium
story_points: 5
component: "Lagerverwaltung"
feature_group: "Inventur"
source_feature: CG-031
---

# WEBWMS-031: Nulldurchgangsinventur implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Nulldurchgangsinventur“ nutzen, damit leere bzw. auf Null gehende Plätze kontrolliert zählen.

## Fachlicher Umfang

Leere bzw. auf Null gehende Plätze kontrolliert zählen.

**Prozesskontext:** Nullbestand → Kontrolle

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Nulldurchgangsinventur“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Leere bzw. auf Null gehende Plätze kontrolliert zählen.
3. Der Ablauf „Nullbestand → Kontrolle“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ZeroCrossingCount, StorageBin.
Vorgesehener Service: ZeroCrossingInventoryService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Bestandsbuchungen, Umlagerungen und Allokationsverbräuche erkennen den Übergang eines Bestands von positiv auf null. Dabei wird innerhalb derselben Transaktion eine eindeutige Kontrollzählung mit Bezug auf die auslösende Ledger-Buchung erzeugt. Bereits offene Nullbestandskontrollen für denselben Bestand werden nicht dupliziert; die Bearbeitung nutzt den vorhandenen Zähl-, Differenz- und Freigabeworkflow.

Nachweise: `DbalInventoryRepository`, die Erweiterungen an `wms_inventory_count` sowie Migration `Version20260918160000`.

API/UI, ticketbezogene Autorisierung und Datenbank-Integrationstests sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-031
- Referenz: https://www.coglas.com/funktionen/
