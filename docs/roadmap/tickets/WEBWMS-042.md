---
id: WEBWMS-042
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Medium
story_points: 13
component: "Transport & Kommissionierung"
feature_group: "Transport"
source_feature: CG-042
---

# WEBWMS-042: Staplerleitsystem implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Staplerleitsystem“ nutzen, damit geplante und automatisch erzeugte Fahrbefehle ausführen.

## Fachlicher Umfang

Geplante und automatisch erzeugte Fahrbefehle ausführen.

**Prozesskontext:** Bedarf → Fahrbefehl → Quittierung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Staplerleitsystem“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Geplante und automatisch erzeugte Fahrbefehle ausführen.
3. Der Ablauf „Bedarf → Fahrbefehl → Quittierung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: TransportOrder, Forklift, TransportTask.
Vorgesehener Service: ForkliftControlService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der Transportleitstand verwaltet Flurförderzeuge und Fahrbefehle mit dem kontrollierten Ablauf offen, zugewiesen, gestartet und abgeschlossen. Produktbezogene Abschlüsse verwenden die bestehende atomare Bestandsumlagerung.

## Quelle

- Feature: CG-042
- Referenz: https://www.coglas.com/funktionen/
