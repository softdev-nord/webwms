---
id: WEBWMS-060
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: High
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Verladung"
source_feature: CG-060
---

# WEBWMS-060: Tourenverwaltung implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Tourenverwaltung“ nutzen, damit sendungen nach Touren und Frachtführern gruppieren.

## Fachlicher Umfang

Sendungen nach Touren und Frachtführern gruppieren.

**Prozesskontext:** Sendungen → Tour → Verladung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Tourenverwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Sendungen nach Touren und Frachtführern gruppieren.
3. Der Ablauf „Sendungen → Tour → Verladung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: TransportTour, TourStop, Shipment.
Vorgesehener Service: TourPlanningService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`LoadingManifest` bündelt etikettierte Sendungen mit Tour- und Fahrzeugreferenz. Über `POST /api/v3/loading-manifests` kann diese Gruppierung mandantengebunden erzeugt und über den Leseendpunkt als Tourfortschritt abgerufen werden. Das V3-Frontend stellt die Manifestübersicht und eine berechtigungsgeschützte Erfassung für noch unverplante Sendungen bereit.

Nachweise: `LoadingApiController`, `V3LoadingController`, `ApiV3QueryService::loadingManifest()`, `ApiV3QueryService::loadingManifests()`, `CreateLoadingManifestHandler`, `LoadingManifest`, `DbalInventoryRepository::saveLoadingManifest()`, `LoadingManifestTest` und `docs/technical/loading-api.md`.

### Abschlussnachweis

`TransportTour`-Persistenz bildet Carrier, Fahrzeug, Abfahrt, Lastgrenze und geordnete Stopps ab. Lademanifeste bleiben der operative Verladungsbeleg einer Tour.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-060
- Referenz: https://www.coglas.com/versand/
