---
id: WEBWMS-014
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: High
story_points: 8
component: "Wareneingang"
feature_group: "Retouren"
source_feature: CG-014
---

# WEBWMS-014: Retourenvereinnahmung implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Retourenvereinnahmung“ nutzen, damit retouren manuell oder automatisch erfassen, prüfen und einlagern.

## Fachlicher Umfang

Retouren manuell oder automatisch erfassen, prüfen und einlagern.

**Prozesskontext:** Retoure → Prüfung → Entscheidung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Retourenvereinnahmung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Retouren manuell oder automatisch erfassen, prüfen und einlagern.
3. Der Ablauf „Retoure → Prüfung → Entscheidung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ReturnOrder, ReturnItem, ReturnDisposition.
Vorgesehener Service: ReturnsService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`ReturnOrder`, `ReturnReceipt` und `ReturnInspection` sind im Wareneingangsleitstand vertikal nutzbar: Ankündigung, Vereinnahmung, Prüfung und Entscheidung mit Wiedereinlagerung, Sperre oder Verschrottung.

## Quelle

- Feature: CG-014
- Referenz: https://www.coglas.com/funktionen/
