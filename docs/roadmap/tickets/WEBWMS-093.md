---
id: WEBWMS-093
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Medium
story_points: 13
component: "Integration & Technik"
feature_group: "Lagertechnik"
source_feature: CG-093
---

# WEBWMS-093: Lagerlifte und Paternoster implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „Lagerlifte und Paternoster“ nutzen, damit automatische Lagergeräte wie Logimat oder Paternoster anbinden.

## Fachlicher Umfang

Automatische Lagergeräte wie Logimat oder Paternoster anbinden.

**Prozesskontext:** Task → Gerät → Rückmeldung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Lagerlifte und Paternoster“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Automatische Lagergeräte wie Logimat oder Paternoster anbinden.
3. Der Ablauf „Task → Gerät → Rückmeldung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: AutomationDevice, DeviceCommand.
Vorgesehener Service: StorageAutomationAdapter.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mit `AutomationDevice`, `DeviceCommand` und `StorageAutomationAdapter` ist der mandantenfähige Kern für Lagerlifte, Paternoster und Logimat-Geräte vorhanden. Migration und DBAL-Repository speichern Geräte, idempotente Befehle, Zustände sowie Benutzer und Zeitpunkte. V3-Weboberfläche und JSON-API bilden Gerätepflege, Task-Zuordnung und Geräterückmeldung berechtigt ab; Demo-Daten und automatisierte Domain-, Application- und Query-Tests decken den Slice ab.

Zur vollständigen Umsetzung fehlen herstellerspezifische Protokolladapter, die tatsächliche asynchrone Übertragung über die Integrations-Outbox und End-to-End-Tests mit einem Geräte-Simulator.

## Quelle

- Feature: CG-093
- Referenz: https://www.coglas.com/hardware-schnittstelle/
