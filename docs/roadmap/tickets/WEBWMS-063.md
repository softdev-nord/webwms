---
id: WEBWMS-063
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
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

**Status:** Done

Manifestpositionen bilden die persistente Ladeliste. `GET /api/v3/loading-manifests/{id}` stellt Sendungsnummer, Trackingnummer, Carrier, Service, Ladezustand und Auditdaten standardisiert bereit. Erstellung, Einzelbestätigung und vollständiger Abschluss sind über getrennte Berechtigungen geschützt und im V3-Frontend als Ladeliste bedienbar.

Nachweise: `LoadingApiController`, `V3LoadingController`, `ApiV3QueryService::loadingManifest()`, `ApiV3QueryService::loadingManifests()`, `LoadingManifest`, Migration `Version20260917143000` sowie die technische und fachliche Dokumentation.

### Abschlussnachweis

Persistente Manifestpositionen bilden standardisierte Ladelisten; zusätzlich können individuelle archivierte Ladelistendokumente erzeugt, geöffnet und gedruckt werden.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-063
- Referenz: https://www.coglas.com/funktionen/
