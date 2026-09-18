---
id: WEBWMS-031
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Offen
priority: Medium
story_points: 5
component: "Lagerverwaltung"
feature_group: "Inventur"
source_feature: CG-031
---

# WEBWMS-031: Nulldurchgangsinventur implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Nulldurchgangsinventur“ nutzen, damit leere bzw. auf Null gehende Plätze kontrolliert zählen.

## Fachlicher Umfang

Leere bzw. auf Null gehende Plätze kontrolliert zählen.

**Prozesskontext:** Nullbestand → Kontrolle

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Nulldurchgangsinventur“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Leere bzw. auf Null gehende Plätze kontrolliert zählen.
3. Der Ablauf „Nullbestand → Kontrolle“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ZeroCrossingCount, StorageBin.
Vorgesehener Service: ZeroCrossingInventoryService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-031
- Referenz: https://www.coglas.com/funktionen/

