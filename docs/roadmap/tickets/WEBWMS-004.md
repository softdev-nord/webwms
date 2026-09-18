---
id: WEBWMS-004
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Offen
priority: High
story_points: 5
component: "Wareneingang"
feature_group: "Erfassung"
source_feature: CG-004
---

# WEBWMS-004: Ungeplanter Wareneingang implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Ungeplanter Wareneingang“ nutzen, damit eingang ohne vorliegenden Bestell- oder Avisbezug erfassen.

## Fachlicher Umfang

Eingang ohne vorliegenden Bestell- oder Avisbezug erfassen.

**Prozesskontext:** Annahme → Identifikation → Buchung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Ungeplanter Wareneingang“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eingang ohne vorliegenden Bestell- oder Avisbezug erfassen.
3. Der Ablauf „Annahme → Identifikation → Buchung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: GoodsReceipt, GoodsReceiptItem, Supplier.
Vorgesehener Service: UnplannedReceiptService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-004
- Referenz: https://www.coglas.com/wareneingang/

