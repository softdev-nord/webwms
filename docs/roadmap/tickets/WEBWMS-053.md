---
id: WEBWMS-053
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Warenausgang & Versand"
feature_group: "Packen"
source_feature: CG-053
---

# WEBWMS-053: Geführter Packprozess implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Geführter Packprozess“ nutzen, damit packplatz führt schrittweise durch Artikel-, Mengen- und Packmittelprüfung.

## Fachlicher Umfang

Packplatz führt schrittweise durch Artikel-, Mengen- und Packmittelprüfung.

**Prozesskontext:** Bereitstellung → Packen → Abschluss

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Geführter Packprozess“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Packplatz führt schrittweise durch Artikel-, Mengen- und Packmittelprüfung.
3. Der Ablauf „Bereitstellung → Packen → Abschluss“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PackingSession, Package, PackageItem.
Vorgesehener Service: PackingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

`PackingOrder`, `PackingPackage` und Packabschluss bilden den fachlichen Domain-, Application- und Persistenzkern. Über API v3 und den V3-Packarbeitsplatz kann eine abgeschlossene Pickliste in einen Packauftrag überführt, in Packstücke aufgeteilt, gelesen und abgeschlossen werden. Mandant und Auditbenutzer stammen aus der jeweiligen authentifizierten Identität.

Nachweise: `PackingApiController`, `ApiV3QueryService::packingOrder()`, `CreatePackingOrderHandler`, `AddPackingPackageHandler`, `CompletePackingOrderHandler`, `DbalInventoryRepository`, `PackingOrderTest`, `PackingPackageTest` sowie `docs/technical/packing-api.md` und `docs/user/packing-api.md`.

Packmittelprüfung und vollständige API-Integrationstests mit MariaDB fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-053
- Referenz: https://www.coglas.com/versand/
