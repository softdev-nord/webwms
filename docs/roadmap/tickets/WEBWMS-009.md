---
id: WEBWMS-009
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: High
story_points: 2
component: "Wareneingang"
feature_group: "Dokumente"
source_feature: CG-009
---

# WEBWMS-009: Anhänge implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Anhänge“ nutzen, damit beliebige Dateien an Aufträge und Prozesse hängen.

## Fachlicher Umfang

Beliebige Dateien an Aufträge und Prozesse hängen.

**Prozesskontext:** Vorgang → Dokument

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Anhänge“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Beliebige Dateien an Aufträge und Prozesse hängen.
3. Der Ablauf „Vorgang → Dokument“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Attachment, DocumentReference.
Vorgesehener Service: DocumentService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Beliebige Prozessnachweise bis 10 MB können an geplante und ungeplante Eingänge, Retouren und Produktionszugänge gehängt, gelistet und mandantensicher heruntergeladen werden.

## Quelle

- Feature: CG-009
- Referenz: https://www.coglas.com/funktionen/
