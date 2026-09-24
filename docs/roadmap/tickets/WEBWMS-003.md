---
id: WEBWMS-003
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: Highest
story_points: 5
component: "Wareneingang"
feature_group: "Erfassung"
source_feature: WEBWMS-REQ-003
---

# WEBWMS-003: Geplanter Wareneingang implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Geplanter Wareneingang“ nutzen, damit wareneingang mit Bestellbezug und Lieferavis erfassen.

## Fachlicher Umfang

Wareneingang mit Bestellbezug und Lieferavis erfassen.

**Prozesskontext:** Bestellung → Avis → Annahme

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Geplanter Wareneingang“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Wareneingang mit Bestellbezug und Lieferavis erfassen.
3. Der Ablauf „Bestellung → Avis → Annahme“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: GoodsReceipt, GoodsReceiptItem, PurchaseOrder.
Vorgesehener Service: GoodsReceiptService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Bestellung, Avis, Annahme, QS und Einlagerung sind durchgängig im Leitstand, der operativen Arbeitsliste und der JSON-API verfügbar. Ungültige Übergänge werden in der Domäne verhindert.

## Quelle

- Feature: WEBWMS-REQ-003
