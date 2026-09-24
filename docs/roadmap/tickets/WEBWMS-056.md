---
id: WEBWMS-056
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Versand"
source_feature: WEBWMS-REQ-056
---

# WEBWMS-056: Versandarten und Carrier implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Versandarten und Carrier“ nutzen, damit versandprodukt, Frachtführer und Service je Sendung bestimmen.

## Fachlicher Umfang

Versandprodukt, Frachtführer und Service je Sendung bestimmen.

**Prozesskontext:** Auftrag → Versandregel → Carrier

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Versandarten und Carrier“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Versandprodukt, Frachtführer und Service je Sendung bestimmen.
3. Der Ablauf „Auftrag → Versandregel → Carrier“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ShippingMethod, Carrier, CarrierService.
Vorgesehener Service: ShippingRuleService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Carrier und Service sind Bestandteil des `Shipment`-Modells und werden über API v3 oder den V3-Versandarbeitsplatz explizit ausgewählt. Beide Zugänge sind mandantengebunden und mit `fulfillment.ship.write` geschützt.

Nachweise: `ShippingApiController::create()`, `CreateShipmentHandler`, `Shipment`, `DbalInventoryRepository::saveShipment()`, `ShipmentTest` sowie `docs/technical/shipping-api.md` und `docs/user/shipping-api.md`.

### Abschlussnachweis

Mandantenfähige Versandregeln wählen Carrier und Service priorisiert nach Gewichtsbereich. Pflege, Vorschau und Regelauswahl stehen in V3-UI und JSON-API bereit.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: WEBWMS-REQ-056
