---
id: WEBWMS-077
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Done
priority: Highest
story_points: 5
component: "Administration"
feature_group: "Benutzer"
source_feature: WEBWMS-REQ-077
---

# WEBWMS-077: Benutzerverwaltung implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Benutzerverwaltung“ nutzen, damit benutzer anlegen, sperren und Standardrollen zuweisen.

## Fachlicher Umfang

Benutzer anlegen, sperren und Standardrollen zuweisen.

**Prozesskontext:** Benutzer → Rolle → Zugriff

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Benutzerverwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Benutzer anlegen, sperren und Standardrollen zuweisen.
3. Der Ablauf „Benutzer → Rolle → Zugriff“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: User, UserStatus, RoleAssignment.
Vorgesehener Service: UserManagementService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`UserAccount`, Status und Rollenzuordnung bilden den fachlichen Kern. Die V3-Administration ermöglicht mandantenbezogenes Anlegen per E-Mail, direkte Änderung der Rollen sowie Aktivierung und Deaktivierung. Fremdmandantenrollen, leere Rollenzuweisungen, Selbstdeaktivierung und der Entzug der eigenen Benutzerverwaltungsberechtigung werden verhindert.

## Quelle

- Feature: WEBWMS-REQ-077
