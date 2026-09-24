---
id: WEBWMS-019
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: High
story_points: 5
component: "Lagerverwaltung"
feature_group: "Bestand"
source_feature: WEBWMS-REQ-019
---

# WEBWMS-019: Sonderbestandskennzeichen implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Sonderbestandskennzeichen“ nutzen, damit bestände nach Eigentum, Status oder Sonderart unterscheiden.

## Fachlicher Umfang

Bestände nach Eigentum, Status oder Sonderart unterscheiden.

**Prozesskontext:** Bestand → Kennzeichen

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Sonderbestandskennzeichen“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestände nach Eigentum, Status oder Sonderart unterscheiden.
3. Der Ablauf „Bestand → Kennzeichen“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockItem, SpecialStockType.
Vorgesehener Service: SpecialStockService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Konfigurierbare Sonderbestandsarten unterscheiden Eigentum, Status und Sonderart. Bestandspositionen werden mit Eigentümerreferenz und Grund klassifiziert; nicht allokierbare Arten werden im Warenausgang ausgeschlossen. V3-UI, JSON-API, Berechtigungen, Mandantentrennung und ein unveränderliches Auditjournal erfüllen die Akzeptanzkriterien.

## Quelle

- Feature: WEBWMS-REQ-019
