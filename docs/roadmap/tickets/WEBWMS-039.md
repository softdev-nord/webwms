---
id: WEBWMS-039
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Teilweise umgesetzt
priority: Highest
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Qualität"
source_feature: CG-039
---

# WEBWMS-039: Barcode-Scan-Kontrolle implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Barcode-Scan-Kontrolle“ nutzen, damit platz, Artikel, Charge, Seriennummer und Menge gegen Auftrag prüfen.

## Fachlicher Umfang

Platz, Artikel, Charge, Seriennummer und Menge gegen Auftrag prüfen.

**Prozesskontext:** Scan → Abgleich → Bestätigung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Barcode-Scan-Kontrolle“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Platz, Artikel, Charge, Seriennummer und Menge gegen Auftrag prüfen.
3. Der Ablauf „Scan → Abgleich → Bestätigung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Barcode, ScanEvent, PickConfirmation.
Vorgesehener Service: ScanValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Pickbestätigung vorhanden; Barcodeprüfung fehlt. Ein Teil der fachlichen oder technischen Grundlage ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-039
- Referenz: https://www.coglas.com/kommissionierung/

