---
id: WEBWMS-034
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Highest
story_points: 5
component: "Transport & Kommissionierung"
feature_group: "Kommissionierung"
source_feature: CG-034
---

# WEBWMS-034: Single-Order-Picking implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Single-Order-Picking“ nutzen, damit einen Kundenauftrag pro Rundgang bearbeiten.

## Fachlicher Umfang

Einen Kundenauftrag pro Rundgang bearbeiten.

**Prozesskontext:** Auftrag → Rundgang → Abschluss

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Single-Order-Picking“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Einen Kundenauftrag pro Rundgang bearbeiten.
3. Der Ablauf „Auftrag → Rundgang → Abschluss“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PickWave, PickTask.
Vorgesehener Service: SingleOrderPickingStrategy.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der explizite Single-Order-Modus ist über die Auftragsressource der API v3 umgesetzt. Pro freigegebenem Kundenauftrag entsteht höchstens eine Pickliste; alle Positionen referenzieren aktive Allokationen genau dieses Auftrags. Der Lifecycle umfasst Erzeugung, Zuweisung, sequenzierte Bearbeitung und automatischen Abschluss.

Nachweise: `PickingApiController`, `CreatePickListHandler`, `AssignPickListHandler`, `ConfirmPickTaskHandler`, `DbalInventoryRepository`, Migration `Version20260918200000` und `PickListTest`.

Die responsive Pickansicht, scannergeeignete Bestätigung und Rundgangoptimierung ergänzen die vorhandene auftragsreine Pickliste zu einem durchgängigen Single-Order-Prozess.

## Quelle

- Feature: CG-034
- Referenz: https://www.coglas.com/kommissionierung/
