---
id: WEBWMS-025
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Teilweise umgesetzt
priority: Highest
story_points: 5
component: "Lagerverwaltung"
feature_group: "Qualität"
source_feature: CG-025
---

# WEBWMS-025: Sperrlisten und Sperrgründe implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Sperrlisten und Sperrgründe“ nutzen, damit konfigurierbare Sperrarten und Gründe verwalten.

## Fachlicher Umfang

Konfigurierbare Sperrarten und Gründe verwalten.

**Prozesskontext:** Sperren → Prüfen → Freigeben

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Sperrlisten und Sperrgründe“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Konfigurierbare Sperrarten und Gründe verwalten.
3. Der Ablauf „Sperren → Prüfen → Freigeben“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: BlockReason, StockBlock.
Vorgesehener Service: StockBlockingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Bestandsstatus `blocked`; Sperrgründe und Sperrlisten fehlen. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-025
- Referenz: https://www.coglas.com/funktionen/

