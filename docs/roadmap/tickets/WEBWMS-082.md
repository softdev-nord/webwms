---
id: WEBWMS-082
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Done
priority: Highest
story_points: 8
component: "Administration"
feature_group: "Betrieb"
source_feature: WEBWMS-REQ-082
---

# WEBWMS-082: Webbasierter Betrieb implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Webbasierter Betrieb“ nutzen, damit pC, Tablet, Scanner und moderne Browser ohne lokale Clientinstallation unterstützen.

## Fachlicher Umfang

PC, Tablet, Scanner und moderne Browser ohne lokale Clientinstallation unterstützen.

**Prozesskontext:** Browser → Anwendung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Webbasierter Betrieb“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: PC, Tablet, Scanner und moderne Browser ohne lokale Clientinstallation unterstützen.
3. Der Ablauf „Browser → Anwendung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: DeviceProfile, Session.
Vorgesehener Service: ResponsiveUi / PWA.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Die responsive V3-Webanwendung wird um installierbare PWA-Metadaten, einen sicher auf statische Assets begrenzten Service Worker sowie konfigurierbare Desktop-, Tablet- und Scannerprofile ergänzt. Authentifizierte Inhalte und API-Daten werden nicht offline gespeichert.

## Quelle

- Feature: WEBWMS-REQ-082
