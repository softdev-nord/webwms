---
id: WEBWMS-005
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Teilweise umgesetzt
priority: Highest
story_points: 5
component: "Wareneingang"
feature_group: "Prüfung"
source_feature: CG-005
---

# WEBWMS-005: Mengen- und Abweichungsprüfung implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Mengen- und Abweichungsprüfung“ nutzen, damit über-, Unter- und Falschlieferungen dokumentieren und Folgeaktionen auslösen.

## Fachlicher Umfang

Über-, Unter- und Falschlieferungen dokumentieren und Folgeaktionen auslösen.

**Prozesskontext:** Zählen → Soll/Ist → Abweichung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Mengen- und Abweichungsprüfung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Über-, Unter- und Falschlieferungen dokumentieren und Folgeaktionen auslösen.
3. Der Ablauf „Zählen → Soll/Ist → Abweichung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ReceiptDiscrepancy, GoodsReceiptItem.
Vorgesehener Service: ReceiptValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mengenvalidierung im regulären Wareneingang. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-005
- Referenz: https://www.coglas.com/wareneingang/

