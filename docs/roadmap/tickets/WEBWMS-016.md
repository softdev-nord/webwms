---
id: WEBWMS-016
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Highest
story_points: 5
component: "Lagerverwaltung"
feature_group: "Transparenz"
source_feature: CG-016
---

# WEBWMS-016: Lagerübersicht implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Lagerübersicht“ nutzen, damit lagertypen, Bereiche, Platzbelegung und freie Kapazität visuell darstellen.

## Fachlicher Umfang

Lagertypen, Bereiche, Platzbelegung und freie Kapazität visuell darstellen.

**Prozesskontext:** Lager → Bereich → Platz

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Lagerübersicht“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lagertypen, Bereiche, Platzbelegung und freie Kapazität visuell darstellen.
3. Der Ablauf „Lager → Bereich → Platz“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Warehouse, StorageBin, BinOccupancy.
Vorgesehener Service: WarehouseOverviewService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`ApiV3QueryService::warehouseOverview()` aggregiert Lager- und Bereichsdaten ohne Kapazitätsdoppelzählung. `warehouseOccupancy()` ergänzt eine lagerweise Platzprojektion mit Topologie, Statusverteilung, enthaltenen Artikeln und Auslastung. Die V3-Oberfläche visualisiert jeden Platz farbcodiert nach frei, belegt, QS, gesperrt oder voll; dieselbe mandantensichere Projektion steht berechtigt als JSON-API bereit und ist automatisiert getestet.

## Quelle

- Feature: CG-016
- Referenz: https://www.coglas.com/funktionen/
