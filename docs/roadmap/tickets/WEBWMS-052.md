---
id: WEBWMS-052
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Offen
priority: High
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Qualität"
source_feature: CG-052
---

# WEBWMS-052: QS-Checklisten im Ausgang implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „QS-Checklisten im Ausgang“ nutzen, damit vollständigkeit, Zustand und kundenspezifische Prüfungen dokumentieren.

## Fachlicher Umfang

Vollständigkeit, Zustand und kundenspezifische Prüfungen dokumentieren.

**Prozesskontext:** Pick → QS → Freigabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „QS-Checklisten im Ausgang“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Vollständigkeit, Zustand und kundenspezifische Prüfungen dokumentieren.
3. Der Ablauf „Pick → QS → Freigabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: OutboundQualityCheck, QualityChecklist.
Vorgesehener Service: OutboundQualityService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-052
- Referenz: https://www.coglas.com/funktionen/

