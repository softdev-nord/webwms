---
id: WEBWMS-086
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Medium
story_points: 13
component: "Integration & Technik"
feature_group: "ERP"
source_feature: CG-086
---

# WEBWMS-086: SAP IDoc implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „SAP IDoc“ nutzen, damit sAP-Datenaustausch über IDoc unterstützen.

## Fachlicher Umfang

SAP-Datenaustausch über IDoc unterstützen.

**Prozesskontext:** SAP → IDoc → Mapping

## Akzeptanzkriterien

1. Berechtigte Benutzer können „SAP IDoc“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: SAP-Datenaustausch über IDoc unterstützen.
3. Der Ablauf „SAP → IDoc → Mapping“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: IdocMessage, IntegrationMapping.
Vorgesehener Service: SapIdocAdapter.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-086
- Referenz: https://www.coglas.com/schnittstellen/
