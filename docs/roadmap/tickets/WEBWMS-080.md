---
id: WEBWMS-080
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Offen
priority: High
story_points: 5
component: "Administration"
feature_group: "Stammdaten"
source_feature: CG-080
---

# WEBWMS-080: Nummernkreise implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Nummernkreise“ nutzen, damit eigene Beleg- und Identnummernkreise einschließlich GS1 konfigurieren.

## Fachlicher Umfang

Eigene Beleg- und Identnummernkreise einschließlich GS1 konfigurieren.

**Prozesskontext:** Objekt → Nummernregel → Vergabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Nummernkreise“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Eigene Beleg- und Identnummernkreise einschließlich GS1 konfigurieren.
3. Der Ablauf „Objekt → Nummernregel → Vergabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: NumberRange, Sequence.
Vorgesehener Service: NumberRangeService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-080
- Referenz: https://www.coglas.com/funktionen/

