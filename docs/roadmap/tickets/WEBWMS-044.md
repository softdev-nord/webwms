---
id: WEBWMS-044
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: Highest
story_points: 5
component: "Transport & Kommissionierung"
feature_group: "Transport"
source_feature: WEBWMS-REQ-044
---

# WEBWMS-044: Umlagerung implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Umlagerung“ nutzen, damit bestand kontrolliert zwischen Plätzen und Bereichen bewegen.

## Fachlicher Umfang

Bestand kontrolliert zwischen Plätzen und Bereichen bewegen.

**Prozesskontext:** Quelle → Transport → Ziel

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Umlagerung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestand kontrolliert zwischen Plätzen und Bereichen bewegen.
3. Der Ablauf „Quelle → Transport → Ziel“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: RelocationOrder, StockMovement.
Vorgesehener Service: RelocationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`StockTransfer` und `TransferStockHandler` bilden den fachlichen Domain-, Application- und Persistenzkern. Der Transportleitstand und die API v3 ergänzen Planung, Ressourcenzuweisung, Ausführung, Autorisierung und Auditierung.

## Quelle

- Feature: WEBWMS-REQ-044
