---
id: WEBWMS-096
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Teilweise umgesetzt
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "Zuverlässigkeit"
source_feature: CG-096
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

Eine persistente, transaktionale Outbox mit Pending-Abfrage und idempotenter Quittierung ist vorhanden. Nicht quittierte Nachrichten bleiben für erneute Abrufe sichtbar; Zielsysteme verwenden die UUIDv7-Nachrichten-ID als Idempotenzschlüssel.

Nachweise: `wms_integration_outbox`, `IntegrationStatusEvent`, `OutboxAcknowledgement`, `DbalOutboxRepository`, `OutboxApiController`, Migration `Version20260919100000` und die Outbox-Dokumentation.

Queue-Publisher, automatische Retry-Strategie, Zustellversuche, Backoff, Dead-Letter-Verarbeitung und Betriebsmetriken fehlen noch; das Ticket bleibt deshalb `Teilweise umgesetzt`.

## Quelle

- Feature: CG-096
- Referenz: https://www.coglas.com/schnittstellen/
