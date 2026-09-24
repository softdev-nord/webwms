---
id: WEBWMS-078
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Done
priority: Highest
story_points: 8
component: "Administration"
feature_group: "Berechtigungen"
source_feature: WEBWMS-REQ-078
---

# WEBWMS-078: Rollen und Rechte implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Rollen und Rechte“ nutzen, damit eigene Rollen und feingliedrige Berechtigungen konfigurieren.

## Fachlicher Umfang

Eigene Rollen und feingliedrige Berechtigungen konfigurieren.

**Prozesskontext:** Rolle → Permission → Prüfung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Rollen und Rechte“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eigene Rollen und feingliedrige Berechtigungen konfigurieren.
3. Der Ablauf „Rolle → Permission → Prüfung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Role, Permission, RolePermission.
Vorgesehener Service: AuthorizationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`Role`, `PermissionKey` und `PermissionVoter` bilden den fachlichen Kern. Eigene Rollen können aus dem kontrollierten Berechtigungskatalog angelegt und bearbeitet werden. Beim Bearbeiten einer selbst verwendeten Rolle verhindert der Service den Entzug der eigenen Rollenverwaltungsberechtigung.

## Quelle

- Feature: WEBWMS-REQ-078
