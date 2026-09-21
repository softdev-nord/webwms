---
id: WEBWMS-011
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Wareneingang"
feature_group: "Einlagerung"
source_feature: CG-011
---

# WEBWMS-011: Einlagerungsauftrag implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Einlagerungsauftrag“ nutzen, damit nach Bestandsbuchung einen geeigneten Zielplatz ermitteln und Transport auslösen.

## Fachlicher Umfang

Nach Bestandsbuchung einen geeigneten Zielplatz ermitteln und Transport auslösen.

**Prozesskontext:** Buchung → Platzfindung → Transport

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Einlagerungsauftrag“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Nach Bestandsbuchung einen geeigneten Zielplatz ermitteln und Transport auslösen.
3. Der Ablauf „Buchung → Platzfindung → Transport“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PutawayTask, StorageBin, HandlingUnit.
Vorgesehener Service: PutawayStrategyService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

`PutawayStrategy`, `PutawayRequest` und `ConfirmPutawayHandler` bilden Platzfindung und atomare Umbuchung ab. V3-Weboberfläche und JSON-API erlauben die berechtigte Planung und Bestätigung; Demo-Daten stellen eine ausführbare Strategie bereit. Strategiepflege und vollständige Akzeptanztests bleiben für `Done` offen.

## Quelle

- Feature: CG-011
- Referenz: https://www.coglas.com/wareneingang/
