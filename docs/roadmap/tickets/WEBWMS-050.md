---
id: WEBWMS-050
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
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

**Status:** Done

Die artikelbezogene Bedarfsvorschau aggregiert offene Ausgangsmengen, verfügbaren Bestand und Engpässe im Warenausgangsleitstand sowie über die JSON-API.

### Abschlussnachweis

`ApiV3QueryService::outboundControlCenter()` aggregiert offene Bedarfe, verfügbare Bestände und Engpassmengen artikelbezogen. Der V3-Leitstand stellt Vorschau und Auftragsprüfung filter- und paginierbar bereit.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-050
- Referenz: https://www.coglas.com/funktionen/
