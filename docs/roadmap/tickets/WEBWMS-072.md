---
id: WEBWMS-072
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Offen
priority: High
story_points: 8
component: "Zusatzfunktionen"
feature_group: "Suche"
source_feature: CG-072
---

# WEBWMS-072: Volltextsuche implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Volltextsuche“ nutzen, damit systemweit nach Inhalten, Belegen und Mustern suchen.

## Fachlicher Umfang

Systemweit nach Inhalten, Belegen und Mustern suchen.

**Prozesskontext:** Suchbegriff → Index → Treffer

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Volltextsuche“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Systemweit nach Inhalten, Belegen und Mustern suchen.
3. Der Ablauf „Suchbegriff → Index → Treffer“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: SearchDocument, SearchIndex.
Vorgesehener Service: GlobalSearchService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-072
- Referenz: https://www.coglas.com/funktionen/

