---
id: WEBWMS-064
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Medium
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Dokumente"
source_feature: WEBWMS-REQ-064
---

# WEBWMS-064: CMR-Frachtbrief implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „CMR-Frachtbrief“ nutzen, damit standardisierten internationalen Frachtbrief erstellen.

## Fachlicher Umfang

Standardisierten internationalen Frachtbrief erstellen.

**Prozesskontext:** Tour → CMR

## Akzeptanzkriterien

1. Berechtigte Benutzer können „CMR-Frachtbrief“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Standardisierten internationalen Frachtbrief erstellen.
3. Der Ablauf „Tour → CMR“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: CmrDocument, TransportTour.
Vorgesehener Service: CmrDocumentService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

CMR-Frachtbriefe werden tour- oder manifestbezogen als nummerierte, unveränderliche und direkt druckbare Dokumente mit Auditdaten und Prüfsumme archiviert.

### Abschlussnachweis

CMR-Frachtbriefe werden tour- oder manifestbezogen als nummerierte, unveränderliche Dokumente mit Prüfsumme und Auditdaten erzeugt und im Dokumentenarchiv bereitgestellt.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: WEBWMS-REQ-064
