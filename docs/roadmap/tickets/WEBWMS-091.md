---
id: WEBWMS-091
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Integration & Technik"
feature_group: "Hardware"
source_feature: CG-091
---

# WEBWMS-091: Drucker implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Drucker“ nutzen, damit etiketten- und Dokumentendrucker ansteuern.

## Fachlicher Umfang

Etiketten- und Dokumentendrucker ansteuern.

**Prozesskontext:** Job → Drucker

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Drucker“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Etiketten- und Dokumentendrucker ansteuern.
3. Der Ablauf „Job → Drucker“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Printer, PrintJob.
Vorgesehener Service: PrintGateway.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `Printer`, `PrintJob`, `PrintGateway` und dem generischen
`HttpPrintTransport` sind mandantensichere Druckerkonfiguration, idempotente
Druckwarteschlange, Zustandsautomat, Wiederholungsversuche und Auditdaten
vorhanden. Die API schützt Verwaltung, Lesenzugriff und Ausführung mit getrennten
Rechten. Nachweise: `PrintApiController`, `Version20260919173000`,
`PrintJobTest`, `HttpPrintTransportTest` und
`docs/technical/print-integration.md`.

Der V3-Arbeitsbereich ergänzt Druckerübersicht, Anlage, Detail- und Auditansicht,
Statuswechsel sowie eine zentrale Druckwarteschlange. Allgemeine Druckaufträge
können erfasst und wartende oder fehlgeschlagene Jobs manuell ausgeführt
beziehungsweise erneut versucht werden. Fehler, Versuche und externe Referenzen
sind in der Detailansicht nachvollziehbar. Nachweise: `V3PrintingController`,
die Templates unter `templates/v3/integration/printing` und die erweiterten
mandantengebundenen Query-Tests.

Standort-/Arbeitsplatz-Routing, herstellerspezifische Adapter, automatische
Worker-Ausführung und vollständige HTTP-/MariaDB-Integrationstests fehlen
weiterhin; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-091
- Referenz: https://www.coglas.com/hardware-schnittstelle/
