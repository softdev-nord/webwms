---
id: WEBWMS-085
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Offen
priority: Highest
story_points: 8
component: "Integration & Technik"
feature_group: "Dateiformate"
source_feature: CG-085
---

# WEBWMS-085: JSON/XML/XLSX/CSV implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „JSON/XML/XLSX/CSV“ nutzen, damit daten über dokumentierte Standardformate importieren und exportieren.

## Fachlicher Umfang

Daten über dokumentierte Standardformate importieren und exportieren.

**Prozesskontext:** Import → Validierung → Verarbeitung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „JSON/XML/XLSX/CSV“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Daten über dokumentierte Standardformate importieren und exportieren.
3. Der Ablauf „Import → Validierung → Verarbeitung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ImportJob, ImportRow, ExportJob.
Vorgesehener Service: DataExchangeService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-085
- Referenz: https://www.coglas.com/schnittstellen/

