---
id: WEBWMS-073
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: Medium
story_points: 5
component: "Zusatzfunktionen"
feature_group: "Medien"
source_feature: CG-073
---

# WEBWMS-073: Kameranutzung implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Kameranutzung“ nutzen, damit dokumente, Artikel und Ladehilfsmittel fotografieren und hinterlegen.

## Fachlicher Umfang

Dokumente, Artikel und Ladehilfsmittel fotografieren und hinterlegen.

**Prozesskontext:** Kamera → Upload → Zuordnung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Kameranutzung“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Dokumente, Artikel und Ladehilfsmittel fotografieren und hinterlegen.
3. Der Ablauf „Kamera → Upload → Zuordnung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: MediaAsset, Attachment.
Vorgesehener Service: MediaCaptureService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Mobile Browser können über `capture=environment` Fotos aufnehmen oder Dokumente hochladen. Erlaubte Formate, 5-MB-Grenze, Mandant, Objektzuordnung, SHA-256-Prüfsumme, Benutzer und Zeitpunkt werden serverseitig erzwungen und persistiert.

Nachweise: `wms_media_asset`, `PlatformControlService::captureMedia()`, geschützter Medienabruf und Kameraformular.

## Quelle

- Feature: CG-073
- Referenz: https://www.coglas.com/funktionen/
