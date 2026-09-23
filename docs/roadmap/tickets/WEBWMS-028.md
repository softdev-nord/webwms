---
id: WEBWMS-028
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Medium
story_points: 5
component: "Lagerverwaltung"
feature_group: "Ladehilfsmittel"
source_feature: CG-028
---

# WEBWMS-028: LHM-Konto implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „LHM-Konto“ nutzen, damit zu- und Abgänge von Paletten, Behältern und Ladehilfsmitteln verbuchen.

## Fachlicher Umfang

Zu- und Abgänge von Paletten, Behältern und Ladehilfsmitteln verbuchen.

**Prozesskontext:** Übergabe → Kontobuchung

## Akzeptanzkriterien

1. Berechtigte Benutzer können „LHM-Konto“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Zu- und Abgänge von Paletten, Behältern und Ladehilfsmitteln verbuchen.
3. Der Ablauf „Übergabe → Kontobuchung“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: LoadCarrier, LoadCarrierAccount, LoadCarrierMovement.
Vorgesehener Service: LoadCarrierAccountService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Umgesetzt im Inventory Control Center mit mandantenfähigem Application Service, transaktionaler Persistenz, REST- und Web-Oberfläche, fachlich getrennten Berechtigungen, Migration, automatisierten Tests sowie technischer und fachlicher Dokumentation.

## Quelle

- Feature: CG-028
- Referenz: https://www.coglas.com/funktionen/

