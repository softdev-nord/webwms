---
id: WEBWMS-032
issue_type: Story
epic: WEBWMS-EPIC-INVENTORY
status: Done
priority: Highest
story_points: 8
component: "Lagerverwaltung"
feature_group: "Inventur"
source_feature: CG-032
---

# WEBWMS-032: Differenz- und Freigabeworkflow implementieren

## User Story

Als Lagerverantwortlicher möchte ich die Funktion „Differenz- und Freigabeworkflow“ nutzen, damit zählabweichungen prüfen, freigeben und buchen.

## Fachlicher Umfang

Zählabweichungen prüfen, freigeben und buchen.

**Prozesskontext:** Zählen → Abweichung → Freigabe

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Differenz- und Freigabeworkflow“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Zählabweichungen prüfen, freigeben und buchen.
3. Der Ablauf „Zählen → Abweichung → Freigabe“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: CountDifference, Approval.
Vorgesehener Service: InventoryApprovalService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INVENTORY. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

`SubmitInventoryCountHandler` und `ApproveInventoryCountHandler` bilden Differenzprüfung, Vier-Augen-Freigabe und atomare Korrekturbuchung mit dem Ledger-Typ `inventory_adjustment` ab. Zwischenzeitliche Bestandsänderungen und aktive Allokationen verhindern eine inkonsistente Freigabe. REST- und Web-Oberfläche, ticketbezogene Autorisierung und automatisierte Tests vervollständigen den vorhandenen Inventur-Kern; das Ticket ist abgeschlossen.

## Quelle

- Feature: CG-032
- Referenz: https://www.coglas.com/lagerverwaltung/
