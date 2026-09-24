---
id: WEBWMS-015
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Struktur"
source_feature: WEBWMS-REQ-015
---

# WEBWMS-015: Lagertopologie implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Lagertopologie“ nutzen, damit standorte, Lager, Bereiche, Gänge, Ebenen und Fächer realitätsnah abbilden.

## Fachlicher Umfang

Standorte, Lager, Bereiche, Gänge, Ebenen und Fächer realitätsnah abbilden.

**Prozesskontext:** Stammdaten → Topologie

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Lagertopologie“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Standorte, Lager, Bereiche, Gänge, Ebenen und Fächer realitätsnah abbilden.
3. Der Ablauf „Stammdaten → Topologie“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Warehouse, WarehouseArea, Aisle, StorageBin.
Vorgesehener Service: WarehouseTopologyService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`WarehouseTopologyService`, `StorageBinDefinition` und die normalisierten Tabellen für Bereich und Gang bilden Standort → Lager → Bereich → Gang → Ebene/Fach vollständig ab. V3-UI und JSON-API, mandantenbezogene Referenzprüfung, Berechtigungen, Auditdaten, Demo-Topologie und automatisierte Domain-/Querytests erfüllen die Akzeptanzkriterien.

## Quelle

- Feature: WEBWMS-REQ-015
