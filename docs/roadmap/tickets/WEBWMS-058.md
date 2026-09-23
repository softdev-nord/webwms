---
id: WEBWMS-058
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Versand"
source_feature: CG-058
---

# WEBWMS-058: Sendungsnummer und Tracking implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Sendungsnummer und Tracking“ nutzen, damit trackingnummer speichern und an ERP/Shop zurückmelden.

## Fachlicher Umfang

Trackingnummer speichern und an ERP/Shop zurückmelden.

**Prozesskontext:** Label → Tracking → Rückmeldung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Sendungsnummer und Tracking“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Trackingnummer speichern und an ERP/Shop zurückmelden.
3. Der Ablauf „Label → Tracking → Rückmeldung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Shipment, TrackingEvent.
Vorgesehener Service: TrackingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Sendungs- und Trackingnummer besitzen mandantenbezogene Eindeutigkeitsregeln und sind über `GET /api/v3/shipments/{id}` abrufbar. Labelregistrierung und Carrier-Übergabe liefern den aktuellen Status und die Trackingnummer zurück.

Nachweise: `ShippingApiController`, `ApiV3QueryService::shipment()`, `ShipmentResult`, die Eindeutigkeitsindizes aus `Version20260917140000` und `docs/technical/shipping-api.md`.

### Abschlussnachweis

Trackingnummern und chronologische Trackingevents sind eindeutig, auditierbar und über UI/API sichtbar. Versand- und Trackingstatus werden über die Outbox an führende Systeme bereitgestellt.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-058
- Referenz: https://www.coglas.com/versand/
