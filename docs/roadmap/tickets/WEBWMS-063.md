---
id: WEBWMS-063
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Backend umgesetzt
priority: High
story_points: 2
component: "Warenausgang & Versand"
feature_group: "Verladung"
source_feature: CG-063
---

# WEBWMS-063: Ladelisten implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Ladelisten“ nutzen, damit standardisierte oder individuelle Ladelisten erzeugen.

## Fachlicher Umfang

Standardisierte oder individuelle Ladelisten erzeugen.

**Prozesskontext:** Tour → Ladeliste

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Ladelisten“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Standardisierte oder individuelle Ladelisten erzeugen.
3. Der Ablauf „Tour → Ladeliste“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: LoadingList, LoadingListItem.
Vorgesehener Service: LoadingListService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Manifestpositionen bilden die persistente Ladeliste. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-063
- Referenz: https://www.coglas.com/funktionen/

