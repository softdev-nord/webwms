---
id: WEBWMS-018
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Historie"
source_feature: WEBWMS-REQ-018
---

# WEBWMS-018: Bewegungshistorie implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Bewegungshistorie“ nutzen, damit alle Lagerbewegungen revisionsnah nachvollziehen.

## Fachlicher Umfang

Alle Lagerbewegungen revisionsnah nachvollziehen.

**Prozesskontext:** Ereignis → Buchung → Historie

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Bewegungshistorie“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Alle Lagerbewegungen revisionsnah nachvollziehen.
3. Der Ablauf „Ereignis → Buchung → Historie“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockMovement, InventoryLedgerEntry.
Vorgesehener Service: InventoryLedgerService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Das unveränderliche `wms_stock_ledger` wird über eine berechtigte, mandantensichere V3-UI und JSON-API revisionsnah bereitgestellt. Filter für Artikel, Lagerplatz, Transfer und Bewegungstyp sowie Benutzer, Grund, Dimensionen, Mengenänderung und resultierender Bestand erfüllen die Nachvollziehbarkeit; Query- und Buchungstests sichern den Ablauf.

## Quelle

- Feature: WEBWMS-REQ-018
