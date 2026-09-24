---
id: WEBWMS-062
issue_type: Story
epic: WEBWMS-EPIC-OUTBOUND
status: Done
priority: Medium
story_points: 5
component: "Warenausgang & Versand"
feature_group: "Verladung"
source_feature: WEBWMS-REQ-062
---

# WEBWMS-062: Gewichtsrestriktionen implementieren

## User Story

Als Mitarbeiter im Warenausgang möchte ich die Funktion „Gewichtsrestriktionen“ nutzen, damit gewichte gegen Paket-, Tour- oder Fahrzeuggrenzen prüfen.

## Fachlicher Umfang

Gewichte gegen Paket-, Tour- oder Fahrzeuggrenzen prüfen.

**Prozesskontext:** Packen/Verladen → Gewichtskontrolle

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Gewichtsrestriktionen“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Gewichte gegen Paket-, Tour- oder Fahrzeuggrenzen prüfen.
3. Der Ablauf „Packen/Verladen → Gewichtskontrolle“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: WeightConstraint, Package, Vehicle.
Vorgesehener Service: WeightValidationService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-OUTBOUND. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Mandantenbezogene Paket-, Carrier-, Fahrzeug- und Tourgrenzen sind konfigurierbar; Paketgrenzen werden im Web- und API-Packprozess vor der Persistenz erzwungen.

### Abschlussnachweis

Konfigurierbare Paket-, Carrier-, Fahrzeug- und Tourgrenzen sind mandantenbezogen persistiert. Das Paketlimit wird in Web und API vor dem Versiegeln erzwungen; Touren zeigen Planlast gegen Maximalgewicht.

Nachweise: `OutboundProcessService`, `ApiV3QueryService::outboundControlCenter()`, V3-Web- und JSON-Controller, Migration `Version20260923160000`, automatisierte Tests sowie die technische und fachliche Dokumentation des Warenausgangsleitstands.

## Quelle

- Feature: WEBWMS-REQ-062
