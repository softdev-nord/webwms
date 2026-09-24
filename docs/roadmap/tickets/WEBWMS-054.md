---
id: WEBWMS-054
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Packen"
source_feature: WEBWMS-REQ-054
---

# WEBWMS-054: Automatischer Mengenabgleich implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Automatischer Mengenabgleich“ nutzen, damit gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.

## Fachlicher Umfang

Gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.

**Prozesskontext:** Scan → Soll/Ist → Abschluss

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Automatischer Mengenabgleich“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.
3. Der Ablauf „Scan → Soll/Ist → Abschluss“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PackingCheck, PackageItem.
Vorgesehener Service: PackingValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der transaktionale Packabschluss vergleicht die erfolgreich gepickten Positionen mit den eindeutig verpackten Positionen und akzeptiert ausschließlich versiegelte Packstücke. Diese Prüfung ist nun über `POST /api/v3/packing-orders/{id}/complete` berechtigt aufrufbar; die Antwort enthält Packstückanzahl und Gesamtgewicht.

Nachweise: `PackingApiController::complete()`, `CompletePackingOrderHandler`, `DbalInventoryRepository::completePackingOrder()`, der eindeutige Index auf `wms_package_item.pick_task_id` und `docs/technical/packing-api.md`.

### Abschlussnachweis

Der transaktionale Packabschluss gleicht gepickte und eindeutig gepackte Positionen ab. Fehlmengen, doppelte Positionen und unversiegelte Pakete verhindern den Abschluss.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: WEBWMS-REQ-054
