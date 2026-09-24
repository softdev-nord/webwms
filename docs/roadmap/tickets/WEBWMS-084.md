---
id: WEBWMS-084
issue_type: Story
epic: WEBWMS-EPIC-INTEGRATION
status: Done
priority: Highest
story_points: 13
component: "Integration & Technik"
feature_group: "API"
source_feature: CG-084
---

# WEBWMS-084: JSON-Web-API implementieren

## User Story

Als Integrationsentwickler möchte ich die Funktion „JSON-Web-API“ nutzen, damit bidirektionale Web-API für Stamm-, Auftrags-, Bestands- und Statusdaten.

## Fachlicher Umfang

Bidirektionale Web-API für Stamm-, Auftrags-, Bestands- und Statusdaten.

**Prozesskontext:** Request/Event → Mapping → Domain

## Akzeptanzkriterien

1. Berechtigte Benutzer können „JSON-Web-API“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Bidirektionale Web-API für Stamm-, Auftrags-, Bestands- und Statusdaten.
3. Der Ablauf „Request/Event → Mapping → Domain“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: ApiClient, ApiCredential, IntegrationMessage.
Vorgesehener Service: WebApiService.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-INTEGRATION. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Backend umgesetzt

Unter `/api/v3` steht eine eigenständige, versionierte JSON-Schnittstelle mit mandantengebundenen API-Key-Clients, granularen Berechtigungen, RFC-7807-artigen Fehlerantworten und Cursor-Paginierung bereit. Als erste vertikale Ressourcen sind Artikel (Lesen/Anlegen), Lager und Echtzeitbestände angebunden. Mandanten-IDs werden ausschließlich aus der authentifizierten Identität übernommen.

Nachweise: `ApiKeyAuthenticator`, `ApiClientUser`, `ApiExceptionSubscriber`, `ApiV3QueryService`, `InventoryApiController`, Konsolenkommando `webwms:api-client:create` und Migration `Version20260918180000`.

Kundenaufträge, Freigaben, Reservierungen, Allokationen sowie auftragsreine Picklisten, Zuweisungen und Pickbestätigungen sind ebenfalls als API-v3-Ressourcen verfügbar. Pack-, Versand- und Verladeprozess ergänzen Packauftrag, Packstücke, Sendung, Tracking, Labelreferenz, Lademanifest und Carrier-Übergabe. Ausgehende Statusereignisse stehen über die mandantengebundene Outbox-API bereit.

Der Bestandsbereich umfasst nun außerdem atomare Umlagerungen und Statusumbuchungen über `POST /api/v3/stock-transfers` sowie das filter- und cursorbasierte Bewegungsjournal unter `GET /api/v3/stock-movements`. Clientseitig vorgegebene Transfer-IDs ermöglichen eine sichere Wiederholungsprüfung.

Nachweise: die API-v3-Controller, `ApiV3QueryService`, `StockMovementCriteria`, der vorhandene `TransferStockHandler`, Unit-Tests sowie `docs/technical/inventory-movement-api.md` und `docs/user/inventory-movement-api.md`.

Ein formaler OpenAPI-Vertrag, vollständige HTTP-Integrationstests mit MariaDB und weitere Stammdatenressourcen fehlen noch; das Ticket ist deshalb nicht `Done`.

## Quelle

- Feature: CG-084
- Referenz: https://www.coglas.com/schnittstellen/
