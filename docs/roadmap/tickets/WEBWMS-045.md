---
id: WEBWMS-045
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Backend umgesetzt
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Nachschub"
source_feature: CG-045
---

# WEBWMS-045: Nachschubsteuerung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Nachschubsteuerung“ nutzen, damit pickplätze nach Mindestbestand, Bedarf oder Zeit versorgen.

## Fachlicher Umfang

Pickplätze nach Mindestbestand, Bedarf oder Zeit versorgen.

**Prozesskontext:** Signal → Bedarf → Nachschub

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Nachschubsteuerung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Pickplätze nach Mindestbestand, Bedarf oder Zeit versorgen.
3. Der Ablauf „Signal → Bedarf → Nachschub“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ReplenishmentRule, ReplenishmentTask.
Vorgesehener Service: ReplenishmentService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`ReplenishmentPolicy`, Quellwahl und Nachschubauftrag. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-045
- Referenz: https://www.coglas.com/funktionen/

