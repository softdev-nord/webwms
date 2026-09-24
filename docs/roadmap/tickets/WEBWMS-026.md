---
id: WEBWMS-026
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Medium
story_points: 8
component: "Lagerverwaltung"
feature_group: "Compliance"
source_feature: WEBWMS-REQ-026
---

# WEBWMS-026: Gefahrstoffverwaltung implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Gefahrstoffverwaltung“ nutzen, damit gefahrstoffe und zulässige Gefahrstoffbereiche führen.

## Fachlicher Umfang

Gefahrstoffe und zulässige Gefahrstoffbereiche führen.

**Prozesskontext:** Artikel → Klassifizierung → Platzprüfung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Gefahrstoffverwaltung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Gefahrstoffe und zulässige Gefahrstoffbereiche führen.
3. Der Ablauf „Artikel → Klassifizierung → Platzprüfung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: HazardousMaterial, HazardClass, StorageRestriction.
Vorgesehener Service: HazardousGoodsService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Umgesetzt im Inventory Control Center mit mandantenfähigem Application Service, transaktionaler Persistenz, REST- und Web-Oberfläche, fachlich getrennten Berechtigungen, Migration, automatisierten Tests sowie technischer und fachlicher Dokumentation.

## Quelle

- Feature: WEBWMS-REQ-026

