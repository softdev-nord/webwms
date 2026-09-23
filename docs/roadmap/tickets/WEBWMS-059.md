---
id: WEBWMS-059
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Highest
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Dokumente"
source_feature: CG-059
---

# WEBWMS-059: Versanddokumente implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Versanddokumente“ nutzen, damit lieferschein, Packliste und weitere Dokumente direkt erzeugen.

## Fachlicher Umfang

Lieferschein, Packliste und weitere Dokumente direkt erzeugen.

**Prozesskontext:** Packabschluss → Dokument

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Versanddokumente“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Lieferschein, Packliste und weitere Dokumente direkt erzeugen.
3. Der Ablauf „Packabschluss → Dokument“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ShippingDocument, DocumentTemplate.
Vorgesehener Service: ShippingDocumentService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Versanddokumente werden nummeriert, mandantenbezogen und mit SHA-256-Prüfsumme als unveränderliche Momentaufnahme archiviert und im Leitstand bereitgestellt.

### Abschlussnachweis

Lieferschein, Packliste und Ladeliste werden als unveränderliche HTML-Momentaufnahme mit Dokumentnummer, Auditdaten und SHA-256-Prüfsumme erzeugt und im V3-Archiv bereitgestellt.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: CG-059
- Referenz: https://www.coglas.com/versand/
