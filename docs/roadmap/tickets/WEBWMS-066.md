---
id: WEBWMS-066
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: Medium
story_points: 5
component: "Zusatzfunktionen"
feature_group: "Steuerung"
source_feature: WEBWMS-REQ-066
---

# WEBWMS-066: Shopfloor implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Shopfloor“ nutzen, damit ein- und Auslagerungen manuell planen und priorisieren.

## Fachlicher Umfang

Ein- und Auslagerungen manuell planen und priorisieren.

**Prozesskontext:** Planung → Aufgabe → Ausführung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Shopfloor“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Ein- und Auslagerungen manuell planen und priorisieren.
3. Der Ablauf „Planung → Aufgabe → Ausführung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ShopfloorTask, TaskPriority.
Vorgesehener Service: ShopfloorPlanningService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der Shopfloor-Arbeitsvorrat plant Einlagerung, Auslagerung, Transport und Inventur mit Priorität, Termin, Referenz und Benutzerzuordnung. Der transaktionale Zustandsautomat verhindert unzulässige Übergänge; UI und API protokollieren jede Änderung.

Nachweise: `PlatformControlService`, `V3PlatformController`, `PlatformApiController`, Migration `Version20260923190000`, Unit-Tests sowie Platform-Benutzer- und Technikdokumentation.

## Quelle

- Feature: WEBWMS-REQ-066
