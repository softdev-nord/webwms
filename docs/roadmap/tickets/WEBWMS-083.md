---
id: WEBWMS-083
issue_type: Story
epic: WEBWMS-EPIC-ADMIN
status: Done
priority: Medium
story_points: 13
component: "Administration"
feature_group: "Betrieb"
source_feature: CG-083
---

# WEBWMS-083: SaaS und On-Premises implementieren

## User Story

Als Systemadministrator möchte ich die Funktion „SaaS und On-Premises“ nutzen, damit cloudbetrieb sowie optional selbst betriebene Installation ermöglichen.

## Fachlicher Umfang

Cloudbetrieb sowie optional selbst betriebene Installation ermöglichen.

**Prozesskontext:** Deployment → Betrieb

## Akzeptanzkriterien

1. Berechtigte Benutzer können „SaaS und On-Premises“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Cloudbetrieb sowie optional selbst betriebene Installation ermöglichen.
3. Der Ablauf „Deployment → Betrieb“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: DeploymentConfiguration.
Vorgesehener Service: DeploymentArchitecture.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-ADMIN. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Ein validiertes Betriebsprofil bildet SaaS-, On-Premises- und Hybridbetrieb mit öffentlicher URL, Storage-Treiber, Queue-Transport und Release-Kanal ab. Es wird mandantenbezogen verwaltet und vollständig auditiert; die Applikation bleibt über Umgebungsvariablen und austauschbare Infrastrukturadapter deploybar.

## Quelle

- Feature: CG-083
- Referenz: https://www.coglas.com/lagerverwaltung/
