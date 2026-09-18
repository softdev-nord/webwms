---
id: WEBWMS-054
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Backend umgesetzt
priority: Highest
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Packen"
source_feature: CG-054
---

# WEBWMS-054: Automatischer Mengenabgleich implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Automatischer Mengenabgleich“ nutzen, damit gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.

## Fachlicher Umfang

Gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.

**Prozesskontext:** Scan → Soll/Ist → Abschluss

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Automatischer Mengenabgleich“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Gepackte Mengen gegen Auftrag und Pickbestätigung prüfen.
3. Der Ablauf „Scan → Soll/Ist → Abschluss“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: PackingCheck, PackageItem.
Vorgesehener Service: PackingValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Vollständigkeitsprüfung beim Packabschluss. Der fachliche Domain-, Application- und Persistenzkern ist vorhanden. API/UI, ticketbezogene Autorisierung und die vollständige Akzeptanztestabdeckung sind noch offen; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-054
- Referenz: https://www.coglas.com/versand/

