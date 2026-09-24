---
id: WEBWMS-074
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: High
story_points: 8
component: "Zusatzfunktionen"
feature_group: "Druck"
source_feature: WEBWMS-REQ-074
---

# WEBWMS-074: Druckersteuerung implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Druckersteuerung“ nutzen, damit druckjobs gezielt an Standort-, Arbeitsplatz- oder Prozessdrucker senden.

## Fachlicher Umfang

Druckjobs gezielt an Standort-, Arbeitsplatz- oder Prozessdrucker senden.

**Prozesskontext:** Dokument → Routing → Drucker

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Druckersteuerung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Druckjobs gezielt an Standort-, Arbeitsplatz- oder Prozessdrucker senden.
3. Der Ablauf „Dokument → Routing → Drucker“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Printer, PrintRoutingRule, PrintJob.
Vorgesehener Service: PrintRoutingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der Druckkern aus `WEBWMS-091` wird um priorisierte Routingregeln nach Dokumenttyp, Standort, Arbeitsplatz und Prozess ergänzt. Die Auswahl liefert ausschließlich einen aktiven Drucker des Mandanten; ohne passende Regel wird der Druck kontrolliert abgewiesen.

Nachweise: `wms_print_routing_rule`, `PlatformControlService::routePrinter()`, Routing-API und Platform Control Center.

## Quelle

- Feature: WEBWMS-REQ-074
