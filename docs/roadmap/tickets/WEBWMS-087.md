---
id: WEBWMS-087
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Teilweise umgesetzt
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "ERP"
source_feature: CG-087
---

# WEBWMS-087: ERP-Integration implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „ERP-Integration“ nutzen, damit aufträge und Stammdaten übernehmen sowie Bestand und Status zurückmelden.

## Fachlicher Umfang

Aufträge und Stammdaten übernehmen sowie Bestand und Status zurückmelden.

**Prozesskontext:** ERP ↔ WMS

## Akzeptanzkriterien

1. Berechtigte Benutzer können „ERP-Integration“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Aufträge und Stammdaten übernehmen sowie Bestand und Status zurückmelden.
3. Der Ablauf „ERP ↔ WMS“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ErpConnection, IntegrationMessage.
Vorgesehener Service: ErpConnector.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Teilweise umgesetzt

Mandantengebundene ERP-Verbindungen können über API v3 registriert, gelesen, aktiviert und pausiert werden. Geheimnisse verbleiben in Laufzeit-Umgebungsvariablen; WebWMS persistiert ausschließlich deren Referenz. Benutzer und Zeitpunkte der Anlage und Statusänderung sind auditierbar.

Artikel- und Auftragsübernahme sowie Bestands- und Bewegungsabfragen nutzen die vorhandenen API-v3-Ressourcen. Ausgehende Statusereignisse verarbeitet `DeliverErpStatusEventHandler` aus der Integrationsqueue und sendet sie per HTTPS mit stabiler Nachrichten-ID und HMAC-SHA-256-Signatur an alle aktiven ERP-Verbindungen des Mandanten. Fehler werden durch Symfony Messenger wiederholt und gegebenenfalls in dessen Failure Queue verschoben.

Der V3-Arbeitsbereich ergänzt eine mandantengebundene Übersicht und Detailansicht sowie CSRF-geschützte Abläufe zum Anlegen, Pausieren und Aktivieren von ERP-Verbindungen. Dabei wird weiterhin ausschließlich die Referenz auf die Credential-Umgebungsvariable erfasst.

Nachweise: `ErpConnection`, `DbalErpConnectionRepository`, `ErpConnectionApiController`, `V3ErpConnectionController`, `DeliverErpStatusEventHandler`, `HttpErpStatusTransport`, Migration `Version20260919140000`, Unit-Tests sowie `docs/technical/erp-integration.md` und `docs/user/erp-integration.md`.

Herstellerspezifische Mappings, aggregierte Betriebsmetriken und vollständige HTTP-/MariaDB-Integrationstests fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-087
- Referenz: https://www.coglas.com/schnittstellen/
