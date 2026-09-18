---
id: WEBWMS-050
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Offen
priority: High
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Planung"
source_feature: CG-050
---

# WEBWMS-050: Auftragsvorschau implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Auftragsvorschau“ nutzen, damit geplante Warenausgänge, Restmengen und Engpässe vorab anzeigen.

## Fachlicher Umfang

Geplante Warenausgänge, Restmengen und Engpässe vorab anzeigen.

**Prozesskontext:** Bedarf → Vorschau → Freigabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Auftragsvorschau“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Geplante Warenausgänge, Restmengen und Engpässe vorab anzeigen.
3. Der Ablauf „Bedarf → Vorschau → Freigabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: OutboundForecast, AllocationShortage.
Vorgesehener Service: OutboundPlanningService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-050
- Referenz: https://www.coglas.com/funktionen/

