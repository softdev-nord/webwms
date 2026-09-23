---
id: WEBWMS-001
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: Highest
story_points: 5
component: "Wareneingang"
feature_group: "Belege"
source_feature: CG-001
---

# WEBWMS-001: Bestellungen implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Bestellungen“ nutzen, damit bestellungen manuell erfassen oder aus ERP/Shop übernehmen.

## Fachlicher Umfang

Bestellungen manuell erfassen oder aus ERP/Shop übernehmen.

**Prozesskontext:** Avis → Bestellung → Lieferung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Bestellungen“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestellungen manuell erfassen oder aus ERP/Shop übernehmen.
3. Der Ablauf „Avis → Bestellung → Lieferung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PurchaseOrder, PurchaseOrderItem, Supplier.
Vorgesehener Service: InboundOrderService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Bestellungen können im Wareneingangsleitstand und per V3-API mandantenbezogen angelegt und bis zur vollständigen Vereinnahmung verfolgt werden. Domain-Handler, Transaktion, Berechtigung und Auditdaten sind integriert.

## Quelle

- Feature: CG-001
- Referenz: https://www.coglas.com/funktionen/
