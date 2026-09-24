---
id: WEBWMS-081
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Done
priority: High
story_points: 8
component: "Administration"
feature_group: "Konfiguration"
source_feature: WEBWMS-REQ-081
---

# WEBWMS-081: Aktivierbare Prozesse implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Aktivierbare Prozesse“ nutzen, damit module und Prozesse bedarfsgerecht aktivieren oder deaktivieren.

## Fachlicher Umfang

Module und Prozesse bedarfsgerecht aktivieren oder deaktivieren.

**Prozesskontext:** Mandant → Feature → Konfiguration

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Aktivierbare Prozesse“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Module und Prozesse bedarfsgerecht aktivieren oder deaktivieren.
3. Der Ablauf „Mandant → Feature → Konfiguration“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: FeatureFlag, ProcessConfiguration.
Vorgesehener Service: FeatureConfigurationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Mandantenbezogene Prozessschalter können über den Admin-Workspace angelegt, aktiviert und mit validierter JSON-Konfiguration versehen werden. Änderungen sind berechtigt, transaktional und auditiert.

## Quelle

- Feature: WEBWMS-REQ-081
