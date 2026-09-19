---
id: WEBWMS-057
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Versand"
source_feature: CG-057
---

# WEBWMS-057: Carrier-Label implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Carrier-Label“ nutzen, damit versandlabel über Carrier-Anbindung erzeugen und drucken.

## Fachlicher Umfang

Versandlabel über Carrier-Anbindung erzeugen und drucken.

**Prozesskontext:** Paket → Carrier API → Label

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Carrier-Label“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Versandlabel über Carrier-Anbindung erzeugen und drucken.
3. Der Ablauf „Paket → Carrier API → Label“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ShipmentLabel, CarrierRequest, PrintJob.
Vorgesehener Service: CarrierLabelService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

`ShipmentLabel` speichert Trackingnummer und externe Labelreferenz über den berechtigten Endpunkt `POST /api/v3/shipments/{id}/label`. Der Zustandswechsel von `prepared` nach `labelled` erfolgt transaktional und wird mit Benutzer und Zeitpunkt auditiert.

Nachweise: `ShippingApiController::registerLabel()`, `RegisterShipmentLabelHandler`, `ShipmentLabel`, `DbalInventoryRepository::registerShipmentLabel()` und `ShipmentTest`.

Die generische Carrier-API erzeugt Label und Trackingdaten idempotent und registriert sie im Versandkern. Im V3-Versandarbeitsplatz kann die Labelreferenz registriert und über `WEBWMS-091` als idempotenter Druckauftrag an ein Print-Gateway übergeben werden. Speicherung der Labeldatei sowie vollständige API-Integrationstests fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-057
- Referenz: https://www.coglas.com/versand/
