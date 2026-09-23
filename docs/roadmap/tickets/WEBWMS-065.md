---
id: WEBWMS-065
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Integration"
source_feature: CG-065
---

# WEBWMS-065: Statusrückmeldung implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Statusrückmeldung“ nutzen, damit pick-, Pack-, Versand- und Trackingstatus an führende Systeme senden.

## Fachlicher Umfang

Pick-, Pack-, Versand- und Trackingstatus an führende Systeme senden.

**Prozesskontext:** Statusereignis → Outbox → Zielsystem

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Statusrückmeldung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Pick-, Pack-, Versand- und Trackingstatus an führende Systeme senden.
3. Der Ablauf „Statusereignis → Outbox → Zielsystem“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: IntegrationEvent, OutboxMessage.
Vorgesehener Service: OutboundStatusPublisher.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Pick-, Pack-, Versand-, Tracking- und Verladeübergänge schreiben innerhalb ihrer Fachtransaktion eine mandantengebundene `IntegrationStatusEvent` in `wms_integration_outbox`. Offene Meldungen können über `GET /api/v3/outbox` cursorbasiert gelesen und nach erfolgreicher Übernahme idempotent quittiert werden. Das V3-Frontend bietet eine tenantbezogene Status- und Payloadansicht sowie die manuelle Pull-Quittierung.

Nachweise: `IntegrationStatusEvent`, `OutboxRepository`, `DbalOutboxRepository`, `OutboxApiController`, `V3OutboxController`, die transaktionalen Aufrufe in `DbalInventoryRepository`, Migration `Version20260919100000`, Unit-Tests sowie `docs/technical/integration-outbox.md` und `docs/user/integration-outbox.md`.

### Abschlussnachweis

Pick-, Pack-, Versand-, Tracking- und Verladeübergänge schreiben mandantengebundene Statusereignisse in die transaktionale Outbox. Pull-Quittierung und aktive ERP-Zustellung sind idempotent sowie auditierbar.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-065
- Referenz: https://www.coglas.com/warenausgang/
