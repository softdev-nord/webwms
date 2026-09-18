---
id: WEBWMS-038
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Mobil"
source_feature: CG-038
---

# WEBWMS-038: Mobile Pickführung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Mobile Pickführung“ nutzen, damit mitarbeitende per Scanner oder Tablet zu Platz, Artikel und Menge führen.

## Fachlicher Umfang

Mitarbeitende per Scanner oder Tablet zu Platz, Artikel und Menge führen.

**Prozesskontext:** Task → Navigation → Bestätigung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Mobile Pickführung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Mitarbeitende per Scanner oder Tablet zu Platz, Artikel und Menge führen.
3. Der Ablauf „Task → Navigation → Bestätigung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MobileTask, PickTask.
Vorgesehener Service: MobileWorkflowService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Pickzuweisung und Bestätigung; mobile Oberfläche fehlt. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-038
- Referenz: https://www.coglas.com/kommissionierung/

