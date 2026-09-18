---
id: WEBWMS-002
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Backend umgesetzt
priority: Highest
story_points: 5
component: "Wareneingang"
feature_group: "Belege"
source_feature: CG-002
---

# WEBWMS-002: Lieferscheine und Avis implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Lieferscheine und Avis“ nutzen, damit lieferscheine manuell oder per Schnittstelle übernehmen und dem Eingang zuordnen.

## Fachlicher Umfang

Lieferscheine manuell oder per Schnittstelle übernehmen und dem Eingang zuordnen.

**Prozesskontext:** Avis → Lieferschein → Eingang

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Lieferscheine und Avis“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lieferscheine manuell oder per Schnittstelle übernehmen und dem Eingang zuordnen.
3. Der Ablauf „Avis → Lieferschein → Eingang“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: InboundDelivery, DeliveryNote, DocumentReference.
Vorgesehener Service: InboundDeliveryService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

`InboundDelivery`, `CreateInboundDeliveryHandler`, `wms_inbound_delivery`. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-002
- Referenz: https://www.coglas.com/wareneingang/

