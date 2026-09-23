---
id: WEBWMS-049
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Aufträge"
source_feature: CG-049
---

# WEBWMS-049: Kundenaufträge implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Kundenaufträge“ nutzen, damit ausgangsaufträge aus ERP/Shop übernehmen, prüfen und verwalten.

## Fachlicher Umfang

Ausgangsaufträge aus ERP/Shop übernehmen, prüfen und verwalten.

**Prozesskontext:** Import → Prüfung → Freigabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Kundenaufträge“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Ausgangsaufträge aus ERP/Shop übernehmen, prüfen und verwalten.
3. Der Ablauf „Import → Prüfung → Freigabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: OutboundOrder, OutboundOrderItem, Customer.
Vorgesehener Service: OutboundOrderService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`OutboundOrder` und `OutboundOrderItem` bilden einen mandantenfähigen 3.0-Kundenauftrag mit eindeutigen Artikeln und validierten Mengen ab. Bei der Freigabe wird für jede Position atomar eine Bestandsreservierung erzeugt. API-v3-Endpunkte und der V3-Auftragsleitstand ermöglichen Anlage, Abfrage und Freigabe mit getrennten Berechtigungen.

Nachweise: `OutboundOrder`, `OutboundOrderRelease`, zugehörige Application-Handler, `DbalInventoryRepository`, `OutboundOrderApiController` und Migration `Version20260918191000`.

### Abschlussnachweis

Der V3-Auftragsarbeitsplatz und die JSON-API bilden Import, Prüfung, begründetes Storno vor Freigabe, Freigabe und Auditierung vollständig ab. Die Freigabe erzeugt transaktional positionsbezogene Reservierungen.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-049
- Referenz: https://www.coglas.com/funktionen/
