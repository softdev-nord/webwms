---
id: WEBWMS-007
issue_type: Story
epic: WEBWMS-EPIC-INBOUND
status: Done
priority: High
story_points: 5
component: "Wareneingang"
feature_group: "Qualität"
source_feature: WEBWMS-REQ-007
---

# WEBWMS-007: Foto-Dokumentation implementieren

## User Story

Als Mitarbeiter im Wareneingang möchte ich die Funktion „Foto-Dokumentation“ nutzen, damit artikelzustand und Schäden am Vorgang fotografisch dokumentieren.

## Fachlicher Umfang

Artikelzustand und Schäden am Vorgang fotografisch dokumentieren.

**Prozesskontext:** Prüfung → Foto → Ablage

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Foto-Dokumentation“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Artikelzustand und Schäden am Vorgang fotografisch dokumentieren.
3. Der Ablauf „Prüfung → Foto → Ablage“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: Attachment, MediaAsset, QualityCheck.
Vorgesehener Service: AttachmentService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Fotos werden über Weboberfläche oder Base64-JSON-API sicher an der Prozessakte gespeichert. Dateigröße, Medientyp, Prüfsumme, Benutzer, Zeitpunkt und Mandant sind nachvollziehbar.

## Quelle

- Feature: WEBWMS-REQ-007
