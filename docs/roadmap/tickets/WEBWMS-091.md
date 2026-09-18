---
id: WEBWMS-091
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Offen
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

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-091
- Referenz: https://www.coglas.com/hardware-schnittstelle/

