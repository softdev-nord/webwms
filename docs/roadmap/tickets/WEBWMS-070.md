---
id: WEBWMS-070
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: Medium
story_points: 13
component: "Zusatzfunktionen"
feature_group: "Abrechnung"
source_feature: CG-070
---

# WEBWMS-070: Lagergeld implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Lagergeld“ nutzen, damit lagerdauer und Bestandsmengen je Mandant abrechnen.

## Fachlicher Umfang

Lagerdauer und Bestandsmengen je Mandant abrechnen.

**Prozesskontext:** Bestandstage → Tarif → Rechnung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Lagergeld“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lagerdauer und Bestandsmengen je Mandant abrechnen.
3. Der Ablauf „Bestandstage → Tarif → Rechnung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StorageFeeRule, BillableStockDay, InvoiceLine.
Vorgesehener Service: StorageBillingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Lagergeldtarife definieren je Geschäftspartner Preis pro Mengeneinheit und Tag, Freitage und Währung. Die Abrechnung erzeugt reproduzierbare offene Belegpositionen aus Menge und Lagerdauer.

Nachweise: `wms_storage_fee_rule`, `wms_billable_line`, `PlatformControlService::billStorage()` sowie Abrechnungsoberfläche.

## Quelle

- Feature: CG-070
- Referenz: https://www.coglas.com/wms-fuer-speditionen-3pl/
