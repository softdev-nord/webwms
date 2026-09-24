---
id: WEBWMS-068
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: Medium
story_points: 8
component: "Zusatzfunktionen"
feature_group: "Portal"
source_feature: WEBWMS-REQ-068
---

# WEBWMS-068: Partnerportal implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Partnerportal“ nutzen, damit separaten Zugang für Geschäftspartner und Mandanten bereitstellen.

## Fachlicher Umfang

Separaten Zugang für Geschäftspartner und Mandanten bereitstellen.

**Prozesskontext:** Partner → Zugriff → Vorgänge

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Partnerportal“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Separaten Zugang für Geschäftspartner und Mandanten bereitstellen.
3. Der Ablauf „Partner → Zugriff → Vorgänge“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PartnerAccount, PortalPermission.
Vorgesehener Service: PartnerPortalService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Partnerkonten verknüpfen einen aktiven Geschäftspartner mit einem mandantengebundenen Benutzer und einem expliziten JSON-Berechtigungssatz. Fremdmandantenreferenzen und Mehrfachzuweisungen werden verhindert.

Nachweise: `wms_partner_account`, `PlatformControlService::create()`, Partnerportal-Tab sowie API.

## Quelle

- Feature: WEBWMS-REQ-068
