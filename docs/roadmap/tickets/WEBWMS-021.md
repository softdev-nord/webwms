---
id: WEBWMS-021
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Backend umgesetzt
priority: High
story_points: 5
component: "Lagerverwaltung"
feature_group: "Rückverfolgung"
source_feature: CG-021
---

# WEBWMS-021: MHD-Verwaltung implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „MHD-Verwaltung“ nutzen, damit mindesthaltbarkeit erfassen, überwachen und für Strategien verwenden.

## Fachlicher Umfang

Mindesthaltbarkeit erfassen, überwachen und für Strategien verwenden.

**Prozesskontext:** Eingang → MHD → Auswahl

## Akzeptanzkriterien

1. Berechtigte Benutzer können „MHD-Verwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Mindesthaltbarkeit erfassen, überwachen und für Strategien verwenden.
3. Der Ablauf „Eingang → MHD → Auswahl“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ExpiryDate, StockItem.
Vorgesehener Service: ExpiryService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

MHD-Dimension und FEFO-fähige Persistenz. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-021
- Referenz: https://www.coglas.com/funktionen/

