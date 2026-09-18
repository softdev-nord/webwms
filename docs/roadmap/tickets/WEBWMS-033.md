---
id: WEBWMS-033
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Backend umgesetzt
priority: Highest
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Kommissionierung"
source_feature: CG-033
---

# WEBWMS-033: Auftragsreine Kommissionierung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Auftragsreine Kommissionierung“ nutzen, damit einen Auftrag wegeoptimiert und getrennt bearbeiten.

## Fachlicher Umfang

Einen Auftrag wegeoptimiert und getrennt bearbeiten.

**Prozesskontext:** Freigabe → Pick → Übergabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Auftragsreine Kommissionierung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Einen Auftrag wegeoptimiert und getrennt bearbeiten.
3. Der Ablauf „Freigabe → Pick → Übergabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PickOrder, PickTask, PickConfirmation.
Vorgesehener Service: PickingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Picklisten werden über `POST /api/v3/orders/{id}/pick-lists` ausschließlich aus aktiven Allokationen eines einzelnen Kundenauftrags gebildet. `PickList::outboundOrderId()`, die persistente Auftragsreferenz und die transaktionale Prüfung in `DbalInventoryRepository::savePickList()` erzwingen die Auftragsreinheit. Zuweisung, Abfrage und Pickbestätigung sind mandantengebunden und durch getrennte Berechtigungen geschützt.

Nachweise: `PickingApiController`, `ApiV3QueryService::pickList()`, `PickList`, `DbalInventoryRepository`, Migration `Version20260918200000`, `PickListTest` sowie die technische und fachliche Dokumentation unter `docs/technical/picking-api.md` und `docs/user/picking-api.md`.

Eine Bedienoberfläche, automatische Wegeoptimierung und vollständige API-Integrationstests mit MariaDB fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-033
- Referenz: https://www.coglas.com/funktionen/
