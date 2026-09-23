---
id: WEBWMS-051
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Reservierung"
source_feature: CG-051
---

# WEBWMS-051: Bestandsreservierung implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Bestandsreservierung“ nutzen, damit geeigneten Bestand verbindlich oder vorläufig einem Auftrag zuordnen.

## Fachlicher Umfang

Geeigneten Bestand verbindlich oder vorläufig einem Auftrag zuordnen.

**Prozesskontext:** Auftrag → Allokation → Reservierung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Bestandsreservierung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Geeigneten Bestand verbindlich oder vorläufig einem Auftrag zuordnen.
3. Der Ablauf „Auftrag → Allokation → Reservierung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockReservation, Allocation.
Vorgesehener Service: AllocationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`StockReservation`, `StockAllocation` und atomare Allokation bilden den fachlichen Kern. Die Kundenauftragsfreigabe erzeugt positionsbezogene Reservierungen; API-v3-Endpunkte und der V3-Auftragsleitstand zeigen den Reservierungsstatus und nehmen Allokationen entgegen. Verfügbarkeit, Restbedarf, Mandant und ausführender Benutzer werden serverseitig abgesichert.

Nachweise: `OutboundOrderApiController`, `AllocateStockHandler`, `DbalInventoryRepository` und `ApiV3QueryService`.

### Abschlussnachweis

Positionsreservierung, manuelle und regelbasierte Allokation, Restbedarf und Verfügbarkeitsprüfung sind mandantenbezogen in Domain, API und V3-Arbeitsplatz integriert.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-051
- Referenz: https://www.coglas.com/warenausgang/
