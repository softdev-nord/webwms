---
id: WEBWMS-008
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Wareneingang"
feature_group: "Qualität"
source_feature: CG-008
---

# WEBWMS-008: Sperrbestand bei Abweichung implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Sperrbestand bei Abweichung“ nutzen, damit beschädigte oder prüfpflichtige Ware in Sperrbestand buchen.

## Fachlicher Umfang

Beschädigte oder prüfpflichtige Ware in Sperrbestand buchen.

**Prozesskontext:** QS → Sperre → Freigabe/Ablehnung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Sperrbestand bei Abweichung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Beschädigte oder prüfpflichtige Ware in Sperrbestand buchen.
3. Der Ablauf „QS → Sperre → Freigabe/Ablehnung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: StockStatus, StockBlock, QualityDecision.
Vorgesehener Service: StockBlockingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Bestandsstatus `blocked` und QS-Entscheidung `block`. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-008
- Referenz: https://www.coglas.com/wareneingang/

