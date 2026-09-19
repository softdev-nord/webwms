---
id: WEBWMS-055
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Teilweise umgesetzt
priority: High
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Packen"
source_feature: CG-055
---

# WEBWMS-055: Scan, Pack & Ship implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Scan, Pack & Ship“ nutzen, damit scanbasierter kombinierter Prozess bis zur Versandfreigabe.

## Fachlicher Umfang

Scanbasierter kombinierter Prozess bis zur Versandfreigabe.

**Prozesskontext:** Scan → Pack → Label → Versand

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Scan, Pack & Ship“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Scanbasierter kombinierter Prozess bis zur Versandfreigabe.
3. Der Ablauf „Scan → Pack → Label → Versand“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PackingSession, Shipment.
Vorgesehener Service: ScanPackShipService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Pack- und Versandkern sind vorhanden. Die API v3 deckt inzwischen den durchgängigen technischen Übergang von Kundenauftrag, Reservierung und Picking über Packauftrag und Packstück bis zu Sendung, Labelregistrierung und Carrier-Übergabe ab. Nachweise: `PackingApiController`, `ShippingApiController`, die Projektionen in `ApiV3QueryService` sowie die technische API-Dokumentation.

Ein scanbasierter Dialog und die tatsächliche Labelerzeugung über eine Carrier-Anbindung fehlen weiterhin; das Ticket bleibt deshalb `Teilweise umgesetzt`.

## Quelle

- Feature: CG-055
- Referenz: https://www.coglas.com/kommissionierung/
