---
id: WEBWMS-061
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: High
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Verladung"
source_feature: WEBWMS-REQ-061
---

# WEBWMS-061: Verladescan implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Verladescan“ nutzen, damit lieferungen beim Verladen gegen Tour und Fahrzeug prüfen.

## Fachlicher Umfang

Lieferungen beim Verladen gegen Tour und Fahrzeug prüfen.

**Prozesskontext:** Scan → Tourabgleich → Verladen

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Verladescan“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lieferungen beim Verladen gegen Tour und Fahrzeug prüfen.
3. Der Ablauf „Scan → Tourabgleich → Verladen“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: LoadingScan, LoadingUnit.
Vorgesehener Service: LoadingValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der Endpunkt `POST /api/v3/loading-manifests/{id}/shipments/{shipmentId}/loading` bestätigt eine Sendung nur dann, wenn sie als offene Position genau diesem Manifest, seiner Tour und seinem Mandanten zugeordnet ist. Benutzer und Zeitpunkt werden je Position gespeichert; doppelte Bestätigungen werden verhindert. Dieselbe Operation steht im V3-Frontend positionsbezogen und CSRF-geschützt zur Verfügung.

Nachweise: `LoadingApiController::confirm()`, `V3LoadingController::confirm()`, `ConfirmShipmentLoadingHandler`, `ShipmentLoading`, `DbalInventoryRepository::confirmShipmentLoading()` und `docs/user/loading-api.md`.

### Abschlussnachweis

Der mandantenbezogene Verladescan validiert Sendung und Manifest, verhindert Doppelbestätigung und protokolliert Benutzer und Zeitpunkt in Weboberfläche und API.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: WEBWMS-REQ-061
