---
id: WEBWMS-024
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: High
story_points: 8
component: "Lagerverwaltung"
feature_group: "Strategien"
source_feature: WEBWMS-REQ-024
---

# WEBWMS-024: FEFO implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „FEFO“ nutzen, damit bestände mit frühestem Ablaufdatum bevorzugen.

## Fachlicher Umfang

Bestände mit frühestem Ablaufdatum bevorzugen.

**Prozesskontext:** Bedarf → MHD-Selektion

## Akzeptanzkriterien

1. Berechtigte Benutzer können „FEFO“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestände mit frühestem Ablaufdatum bevorzugen.
3. Der Ablauf „Bedarf → MHD-Selektion“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockSelectionRule, ExpiryDate.
Vorgesehener Service: FefoAllocationStrategy.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

FEFO ist als allgemeine Entnahmeregel für Ausgangsreservierungen umgesetzt. Noch gültige Bestände mit dem frühesten MHD werden bevorzugt, Bestände ohne MHD folgen und abgelaufene oder nicht allokierbare Bestände bleiben ausgeschlossen. V3-Oberfläche, API, Auditjournal, Demodaten, Berechtigungen und Tests sind vorhanden.

## Quelle

- Feature: WEBWMS-REQ-024
