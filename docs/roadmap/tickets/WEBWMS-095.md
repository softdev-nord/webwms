---
id: WEBWMS-095
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Teilweise umgesetzt
priority: Medium
story_points: 8
component: "Integration & Technik"
feature_group: "Protokolle"
source_feature: CG-095
---

# WEBWMS-095: TCP/IP und Webservice implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „TCP/IP und Webservice“ nutzen, damit hardware und Anlagen über TCP/IP oder Webservices verbinden.

## Fachlicher Umfang

Hardware und Anlagen über TCP/IP oder Webservices verbinden.

**Prozesskontext:** Adapter → Protokoll → Gerät

## Akzeptanzkriterien

1. Berechtigte Benutzer können „TCP/IP und Webservice“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Hardware und Anlagen über TCP/IP oder Webservices verbinden.
3. Der Ablauf „Adapter → Protokoll → Gerät“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Endpoint, ProtocolConfiguration.
Vorgesehener Service: IntegrationTransportService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `TransportEndpoint`, `ProtocolConfiguration` und `IntegrationTransportService` ist der mandantenfähige Konfigurationskern für TCP-Clients sowie REST-/SOAP-Webservices vorhanden. Migration und DBAL-Repository speichern Adapter, Protokoll, Framing, Timeouts und Auditdaten transaktional. V3-Weboberfläche und JSON-API bilden Anlage und Aktivierung berechtigt ab; Demo-Daten sowie automatisierte Domain-, Application- und Query-Tests decken den Slice ab.

Zur vollständigen Umsetzung fehlen die laufzeitseitigen Socket-/HTTP-Transportadapter, Zustellung über die Integrations-Outbox, TLS-/Retry-Policies und End-to-End-Tests mit simulierten Geräten.

## Quelle

- Feature: CG-095
- Referenz: https://www.coglas.com/hardware-schnittstelle/
