---
id: WEBWMS-079
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Offen
priority: Medium
story_points: 8
component: "Administration"
feature_group: "Sicherheit"
source_feature: CG-079
---

# WEBWMS-079: Single Sign-on implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Single Sign-on“ nutzen, damit externe Identity Provider per SSO anbinden.

## Fachlicher Umfang

Externe Identity Provider per SSO anbinden.

**Prozesskontext:** IdP → Login → Session

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Single Sign-on“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Externe Identity Provider per SSO anbinden.
3. Der Ablauf „IdP → Login → Session“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: IdentityProvider, ExternalIdentity.
Vorgesehener Service: SsoService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-079
- Referenz: https://www.coglas.com/funktionen/

