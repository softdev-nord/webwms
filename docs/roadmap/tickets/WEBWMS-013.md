---
id: WEBWMS-013
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: Medium
story_points: 8
component: "Wareneingang"
feature_group: "Produktion"
source_feature: WEBWMS-REQ-013
---

# WEBWMS-013: Wareneingang aus Produktion implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Wareneingang aus Produktion“ nutzen, damit fertigwaren automatisch aus Fertigungsrückmeldungen einlagern.

## Fachlicher Umfang

Fertigwaren automatisch aus Fertigungsrückmeldungen einlagern.

**Prozesskontext:** Produktion → Fertigmeldung → Eingang

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Wareneingang aus Produktion“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Fertigwaren automatisch aus Fertigungsrückmeldungen einlagern.
3. Der Ablauf „Produktion → Fertigmeldung → Eingang“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ProductionReceipt, ProductionOrder.
Vorgesehener Service: ProductionReceiptService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Fertigmeldungen werden im Leitstand oder per V3-API mit Fertigungsauftrag, Artikel, Platz, Menge und Charge erfasst und über den gemeinsamen Bestandsledger atomar eingebucht.

## Quelle

- Feature: WEBWMS-REQ-013
