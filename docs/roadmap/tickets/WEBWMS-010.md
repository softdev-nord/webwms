---
id: WEBWMS-010
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: Highest
story_points: 5
component: "Wareneingang"
feature_group: "Kennzeichnung"
source_feature: CG-010
---

# WEBWMS-010: Etikettendruck implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Etikettendruck“ nutzen, damit wareneingangs-, Artikel- oder Ladeeinheitenetiketten aus Vorlagen drucken.

## Fachlicher Umfang

Wareneingangs-, Artikel- oder Ladeeinheitenetiketten aus Vorlagen drucken.

**Prozesskontext:** Buchung → Label → Druck

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Etikettendruck“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Wareneingangs-, Artikel- oder Ladeeinheitenetiketten aus Vorlagen drucken.
3. Der Ablauf „Buchung → Label → Druck“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: LabelTemplate, PrintJob, HandlingUnit.
Vorgesehener Service: LabelPrintService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Der Leitstand und die V3-API erzeugen auditierte Druckaufträge für Wareneingangs-, Artikel- und Ladeeinheitenetiketten mit kontrollierter Kopienzahl.

## Quelle

- Feature: CG-010
- Referenz: https://www.coglas.com/funktionen/
