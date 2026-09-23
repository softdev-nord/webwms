---
id: WEBWMS-027
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Medium
story_points: 8
component: "Lagerverwaltung"
feature_group: "Produktion"
source_feature: CG-027
---

# WEBWMS-027: Stücklisten implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Stücklisten“ nutzen, damit komponenten für Erzeugnisse planen und bereitstellen.

## Fachlicher Umfang

Komponenten für Erzeugnisse planen und bereitstellen.

**Prozesskontext:** Stückliste → Bedarf → Bereitstellung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Stücklisten“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Komponenten für Erzeugnisse planen und bereitstellen.
3. Der Ablauf „Stückliste → Bedarf → Bereitstellung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: BillOfMaterial, BomItem, MaterialRequirement.
Vorgesehener Service: BomService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Umgesetzt im Inventory Control Center mit mandantenfähigem Application Service, transaktionaler Persistenz, REST- und Web-Oberfläche, fachlich getrennten Berechtigungen, Migration, automatisierten Tests sowie technischer und fachlicher Dokumentation.

## Quelle

- Feature: CG-027
- Referenz: https://www.coglas.com/funktionen/

