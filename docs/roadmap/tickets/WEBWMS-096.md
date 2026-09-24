---
id: WEBWMS-096
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "Zuverlässigkeit"
source_feature: WEBWMS-REQ-096
---

# WEBWMS-096: Asynchrone Integrationsverarbeitung implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Asynchrone Integrationsverarbeitung“ nutzen, damit fehlerwiederholung, Idempotenz und Statusüberwachung für Schnittstellen vorsehen.

## Fachlicher Umfang

Fehlerwiederholung, Idempotenz und Statusüberwachung für Schnittstellen vorsehen.

**Prozesskontext:** Outbox → Queue → Retry

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Asynchrone Integrationsverarbeitung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Fehlerwiederholung, Idempotenz und Statusüberwachung für Schnittstellen vorsehen.
3. Der Ablauf „Outbox → Queue → Retry“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: OutboxMessage, IntegrationAttempt, DeadLetter.
Vorgesehener Service: IntegrationPipelineService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Eine persistente, transaktionale Outbox mit Pending-Abfrage und idempotenter Quittierung ist vorhanden. Der automatische Publisher beansprucht fällige Nachrichten konkurenzsicher und stellt sie als `PublishedIntegrationMessage` in einen persistenten Symfony-Messenger-Transport. Zielsysteme verwenden die unveränderte UUIDv7-Nachrichten-ID als Idempotenzschlüssel.

Queue-Fehler erzeugen persistente Zustellversuche und exponentielle Wiederholungen. Nach fünf Fehlern folgt `dead_letter`; API und V3-Frontend bieten Statusüberwachung sowie eine autorisierte, auditierte manuelle Wiederaufnahme.

Nachweise: `OutboxPublisher`, `MessengerOutboxTransport`, `DbalOutboxRepository`, `PublishOutboxConsoleCommand`, `OutboxApiController`, `V3OutboxController`, `wms_integration_attempt`, Migration `Version20260919120000`, Unit-Tests sowie die technische und Anwenderdokumentation.

Weitere Zieladapter, aggregierte Betriebsmetriken, Alarmierung und vollständige Integrationstests mit MariaDB fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: WEBWMS-REQ-096
