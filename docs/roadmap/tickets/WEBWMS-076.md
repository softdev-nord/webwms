---
id: WEBWMS-076
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Backend umgesetzt
priority: High
story_points: 8
component: "Administration"
feature_group: "Standorte"
source_feature: CG-076
---

# WEBWMS-076: Mehrlager und Standorte implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Mehrlager und Standorte“ nutzen, damit mehrere Lager und Standorte innerhalb eines Systems verwalten.

## Fachlicher Umfang

Mehrere Lager und Standorte innerhalb eines Systems verwalten.

**Prozesskontext:** Organisation → Standort → Lager

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Mehrlager und Standorte“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Mehrere Lager und Standorte innerhalb eines Systems verwalten.
3. Der Ablauf „Organisation → Standort → Lager“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Site, Warehouse, TenantWarehouse.
Vorgesehener Service: SiteManagementService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`Site`, `Warehouse` und mandantenbezogene Lagerplätze. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-076
- Referenz: https://www.coglas.com/wms-fuer-speditionen-3pl/

