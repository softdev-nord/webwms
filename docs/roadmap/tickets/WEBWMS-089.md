---
id: WEBWMS-089
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Offen
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "Carrier"
source_feature: CG-089
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

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-089
- Referenz: https://www.coglas.com/schnittstellen/

