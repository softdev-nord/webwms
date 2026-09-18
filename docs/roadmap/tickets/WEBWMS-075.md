---
id: WEBWMS-075
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Teilweise umgesetzt
priority: High
story_points: 13
component: "Administration"
feature_group: "Mandanten"
source_feature: CG-075
---

# WEBWMS-075: Geschäftspartner und Mandanten implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „Geschäftspartner und Mandanten“ nutzen, damit bestände, Prozesse und Abrechnung mehrerer Kunden trennen.

## Fachlicher Umfang

Bestände, Prozesse und Abrechnung mehrerer Kunden trennen.

**Prozesskontext:** Mandant → Datenraum → Prozess

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Geschäftspartner und Mandanten“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bestände, Prozesse und Abrechnung mehrerer Kunden trennen.
3. Der Ablauf „Mandant → Datenraum → Prozess“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Tenant, BusinessPartner, TenantContext.
Vorgesehener Service: TenantService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mandantenisolation vorhanden; Geschäftspartner und Abrechnung fehlen. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-075
- Referenz: https://www.coglas.com/funktionen/

