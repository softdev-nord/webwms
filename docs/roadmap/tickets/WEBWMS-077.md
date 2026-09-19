---
id: WEBWMS-077
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Teilweise umgesetzt
priority: Highest
story_points: 5
component: "Administration"
feature_group: "Benutzer"
source_feature: CG-077
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

**Status:** Teilweise umgesetzt

`UserAccount`, Status und Rollenzuordnung bilden den fachlichen Domain-, Application- und Persistenzkern. Die V3-Administration ermöglicht mandantenbezogenes Anlegen per E-Mail, Rollenzuordnung sowie Aktivierung und Deaktivierung; Selbstdeaktivierung ist gesperrt. Passwortwechsel, Einladungsprozess und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-077
- Referenz: https://www.coglas.com/funktionen/
