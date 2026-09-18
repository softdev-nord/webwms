---
id: WEBWMS-006
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Backend umgesetzt
priority: High
story_points: 8
component: "Wareneingang"
feature_group: "Qualität"
source_feature: CG-006
---

# WEBWMS-006: Digitale QS-Checklisten implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Digitale QS-Checklisten“ nutzen, damit prüfpflichten und Checklisten mobil abarbeiten.

## Fachlicher Umfang

Prüfpflichten und Checklisten mobil abarbeiten.

**Prozesskontext:** Prüfung → Entscheidung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Digitale QS-Checklisten“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Prüfpflichten und Checklisten mobil abarbeiten.
3. Der Ablauf „Prüfung → Entscheidung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: QualityChecklist, QualityCheck, QualityResult.
Vorgesehener Service: QualityInspectionService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`InboundInspection`, `QualityCheckAnswer`, `wms_inbound_quality_answer`. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-006
- Referenz: https://www.coglas.com/wareneingang/

