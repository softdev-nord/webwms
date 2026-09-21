---
id: WEBWMS-022
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: High
story_points: 8
component: "Lagerverwaltung"
feature_group: "Rückverfolgung"
source_feature: CG-022
---

# WEBWMS-022: Seriennummernverwaltung implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Seriennummernverwaltung“ nutzen, damit eindeutige Seriennummern über Ein-, Um- und Ausgang verfolgen.

## Fachlicher Umfang

Eindeutige Seriennummern über Ein-, Um- und Ausgang verfolgen.

**Prozesskontext:** Eingang → Bewegung → Ausgang

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Seriennummernverwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eindeutige Seriennummern über Ein-, Um- und Ausgang verfolgen.
3. Der Ablauf „Eingang → Bewegung → Ausgang“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: SerialNumber, SerializedStock.
Vorgesehener Service: SerialNumberService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Seriennummern besitzen weiterhin die Mengenregel eins und werden zusätzlich mandantenweit gegen positive Doppelbestände geschützt. Erfassung, Umlagerung und dimensionsgenaue Allokation sind durchgängig; V3-UI und JSON-API zeigen aktuellen Lagerplatz, Status und vollständigen Lebenslauf.

## Quelle

- Feature: CG-022
- Referenz: https://www.coglas.com/funktionen/
