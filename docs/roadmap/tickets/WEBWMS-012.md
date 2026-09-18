---
id: WEBWMS-012
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Offen
priority: Medium
story_points: 8
component: "Wareneingang"
feature_group: "Optimierung"
source_feature: CG-012
---

# WEBWMS-012: Cross-Docking implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Cross-Docking“ nutzen, damit eingang ohne reguläre Einlagerung direkt einem Ausgangsbedarf zuordnen.

## Fachlicher Umfang

Eingang ohne reguläre Einlagerung direkt einem Ausgangsbedarf zuordnen.

**Prozesskontext:** Eingang → Bedarf → Bereitstellung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Cross-Docking“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eingang ohne reguläre Einlagerung direkt einem Ausgangsbedarf zuordnen.
3. Der Ablauf „Eingang → Bedarf → Bereitstellung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: CrossDockAssignment, OutboundOrderItem.
Vorgesehener Service: CrossDockingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-012
- Referenz: https://www.coglas.com/wareneingang/

