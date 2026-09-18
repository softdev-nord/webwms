---
id: WEBWMS-071
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Offen
priority: Medium
story_points: 13
component: "Zusatzfunktionen"
feature_group: "Abrechnung"
source_feature: CG-071
---

# WEBWMS-071: Dienstleistungen und VAS implementieren

## User Story

Als Lagerleiter möchte ich die Funktion „Dienstleistungen und VAS“ nutzen, damit zusatzleistungen und Werkverträge je Mandant erfassen und abrechnen.

## Fachlicher Umfang

Zusatzleistungen und Werkverträge je Mandant erfassen und abrechnen.

**Prozesskontext:** Leistung → Tarif → Rechnung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Dienstleistungen und VAS“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Zusatzleistungen und Werkverträge je Mandant erfassen und abrechnen.
3. Der Ablauf „Leistung → Tarif → Rechnung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ValueAddedService, ServiceEntry, BillingRate.
Vorgesehener Service: ServiceBillingService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-PLATFORM. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Offen

Für dieses Ticket ist noch keine relevante WebWMS-3.0-Implementierung vorhanden.

## Quelle

- Feature: CG-071
- Referenz: https://www.coglas.com/funktionen/

