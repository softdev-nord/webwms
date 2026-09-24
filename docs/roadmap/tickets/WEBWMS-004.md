---
id: WEBWMS-004
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: High
story_points: 5
component: "Wareneingang"
feature_group: "Erfassung"
source_feature: WEBWMS-REQ-004
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

**Status:** Done

Mit `UnplannedReceipt`, `UnplannedReceiptItem` und `UnplannedReceiptService` ist der Ablauf Annahme, Lieferanten-/Artikelidentifikation und atomare Bestandsbuchung ohne Bestellung oder Avis vorhanden. Migration und DBAL-Persistenz speichern Lieferant, Positionen, Bestandsdimensionen und Auditdaten mandantenbezogen. V3-Weboberfläche und JSON-API bilden Annahme und Buchung berechtigt ab; Domain-, Application- und Query-Tests decken zentrale Regeln ab.

Der gemeinsame Wareneingangsleitstand verbindet geplante und ungeplante Eingänge mit QS, Nachweisen, Kennzeichnung und den Folgeprozessen. Mehrere Positionen werden bereits von Service und JSON-API unterstützt.

## Quelle

- Feature: WEBWMS-REQ-004
