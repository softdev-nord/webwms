---
id: WEBWMS-067
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: High
story_points: 8
component: "Zusatzfunktionen"
feature_group: "Reporting"
source_feature: WEBWMS-REQ-067
---

# WEBWMS-067: KPI und Dashboards implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „KPI und Dashboards“ nutzen, damit eigene Lagerkennzahlen und Dashboards konfigurieren.

## Fachlicher Umfang

Eigene Lagerkennzahlen und Dashboards konfigurieren.

**Prozesskontext:** Ereignisse → Aggregation → Anzeige

## Akzeptanzkriterien

1. Berechtigte Benutzer können „KPI und Dashboards“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eigene Lagerkennzahlen und Dashboards konfigurieren.
3. Der Ablauf „Ereignisse → Aggregation → Anzeige“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: KpiDefinition, KpiValue, Dashboard.
Vorgesehener Service: KpiService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Konfigurierbare KPI-Definitionen aggregieren Bestand, offene Ein- und Ausgangsvorgänge, Shopfloor-Aufgaben und Druckjobs. Zielwerte sowie persistente JSON-Dashboardlayouts werden mandantenbezogen und auditiert über UI/API verwaltet.

Nachweise: `PlatformControlService::workspace()`, `wms_kpi_definition`, `wms_dashboard`, Platform Control Center und API.

## Quelle

- Feature: WEBWMS-REQ-067
