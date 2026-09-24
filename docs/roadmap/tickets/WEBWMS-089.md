---
id: WEBWMS-089
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "Carrier"
source_feature: WEBWMS-REQ-089
---

# WEBWMS-089: Carrier-Integration implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Carrier-Integration“ nutzen, damit carrierprodukte, Labels, Manifest und Tracking integrieren.

## Fachlicher Umfang

Carrierprodukte, Labels, Manifest und Tracking integrieren.

**Prozesskontext:** WMS ↔ Carrier

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Carrier-Integration“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Carrierprodukte, Labels, Manifest und Tracking integrieren.
3. Der Ablauf „WMS ↔ Carrier“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: CarrierConnection, CarrierRequest.
Vorgesehener Service: CarrierConnector.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `CarrierConnection`, `CarrierRequest`, `CarrierGateway` und dem generischen
`HttpCarrierTransport` sind Carrierprodukte, Labelerzeugung, Tracking und
Manifestübergabe mandantensicher angebunden. Credential-Referenzen statt Secrets,
idempotente Requests, Auditdaten, granulare API-Rechte, Migration und Tests sind
enthalten. Nachweise: `CarrierApiController`, `Version20260919160000`,
`HttpCarrierTransportTest` und `docs/technical/carrier-integration.md`.

Der V3-Arbeitsbereich ergänzt eine mandantengebundene Verbindungsverwaltung mit
Detail- und Auditansicht, Statuswechseln und Live-Abruf der Versandprodukte.
Vorbereitete Sendungen können Carrier-Labels idempotent anfordern und übernehmen;
der vorhandene Druckworkflow verarbeitet die daraus resultierende Labelreferenz.
Nachweise: `V3CarrierConnectionController`, `V3ShippingController` und die
Templates unter `templates/v3/integration/carrier-connection`.

Herstellerspezifische Adapter, Webhooks für proaktive Trackingereignisse,
die Manifestübergabe im V3-Frontend und vollständige HTTP-/MariaDB-
Integrationstests fehlen weiterhin; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: WEBWMS-REQ-089
