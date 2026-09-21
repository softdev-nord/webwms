# WebWMS 3.0 Dokumentation

Die Dokumentation ist in technische Informationen für Entwicklung und Betrieb
sowie Anwenderanleitungen für die fachliche Nutzung getrennt.

Die versionierte Funktions- und Ticketplanung liegt unter [Roadmap](roadmap/README.md).

## Technische Dokumentation

- [Phase 1: Runtime und Qualitätsbasis](technical/phase-1-runtime.md)
- [Phase 2: Mandanten und Standorte](technical/phase-2-administration.md)
- [Benutzer, Rollen, Rechte und Symfony Security](technical/access-security.md)
- [Inventory Core](technical/inventory-core.md)
- [Bestandsmerkmale: Status, Charge, Seriennummer und MHD](technical/inventory-stock-dimensions.md)
- [Sonderbestände und operative Rückverfolgung](technical/stock-traceability.md)
- [FIFO, LIFO und FEFO](technical/stock-selection-strategies.md)
- [Sperrgründe und Bestandssperren](technical/stock-blocking.md)
- [Atomare Bestandsumlagerung und Statusumbuchung](technical/inventory-transfers.md)
- [Bestandsreservierung und Auftragsallokation](technical/inventory-reservations.md)
- [Picking und Fulfillment](technical/inventory-fulfillment.md)
- [Picklisten und Pickaufträge](technical/inventory-pick-lists.md)
- [Packprozess](technical/inventory-packing.md)
- [Versandprozess](technical/inventory-shipping.md)
- [Verladung, Touren und Manifeste](technical/inventory-loading.md)
- [Retouren- und Rücknahmeabwicklung](technical/inventory-returns.md)
- [Wareneingang, Bestellungen, Avis und QS](technical/inventory-inbound.md)
- [Automatische Einlagerung und Lagerplatzstrategien](technical/inventory-putaway.md)
- [Nachschubsteuerung](technical/inventory-replenishment.md)
- [Stichtagsinventur und Differenzfreigabe](technical/inventory-counting.md)
- [Permanente und Nulldurchgangsinventur](technical/cycle-counting.md)
- [Versionierte JSON-Web-API v3](technical/api-v3.md)
- [Kundenauftrag, Reservierung und Allokation](technical/outbound-order-api.md)
- [Picklisten und Pickaufträge über API v3](technical/picking-api.md)
- [Packprozess über API v3](technical/packing-api.md)
- [Versandprozess über API v3](technical/shipping-api.md)
- [Ladelisten und Verladung über API v3](technical/loading-api.md)
- [Statusrückmeldung und Integrations-Outbox](technical/integration-outbox.md)
- [Bestandsbewegungen in API v3](technical/inventory-movement-api.md)
- [Generische ERP-Integration](technical/erp-integration.md)
- [Architekturübersicht](architecture/README.md)

## Anwenderdokumentation

- [WebWMS 3.0 Grundlagen](user/getting-started.md)
- [Mandanten und Standorte](user/tenants-and-sites.md)
- [Benutzer, Rollen, Rechte und Anmeldung](user/access-security.md)
- [Artikel, Lagerplätze und Bestände](user/inventory-core.md)
- [Bestandsstatus, Chargen, Seriennummern und MHD](user/inventory-stock-dimensions.md)
- [Sonderbestände und Rückverfolgung](user/stock-traceability.md)
- [FIFO, LIFO und FEFO verwenden](user/stock-selection-strategies.md)
- [Bestand sperren und freigeben](user/stock-blocking.md)
- [Bestand umlagern und Status ändern](user/inventory-transfers.md)
- [Bestand reservieren und allokieren](user/inventory-reservations.md)
- [Allokationen freigeben und entnehmen](user/inventory-fulfillment.md)
- [Mit Picklisten arbeiten](user/inventory-pick-lists.md)
- [Packaufträge und Packstücke](user/inventory-packing.md)
- [Sendungen vorbereiten und übergeben](user/inventory-shipping.md)
- [Sendungen verladen](user/inventory-loading.md)
- [Retouren annehmen und prüfen](user/inventory-returns.md)
- [Bestellungen und Wareneingänge bearbeiten](user/inventory-inbound.md)
- [Ware automatisch einlagern](user/inventory-putaway.md)
- [Kommissionierplätze nachfüllen](user/inventory-replenishment.md)
- [Kommissionierung und internen Transport steuern](user/advanced-fulfillment-control.md)
- [Stichtagsinventur durchführen](user/inventory-counting.md)
- [Permanente und Nulldurchgangsinventur verwenden](user/cycle-counting.md)
- [JSON-Web-API v3 verwenden](user/api-v3.md)
- [Kundenaufträge über API v3 bearbeiten](user/outbound-order-api.md)
- [Kommissionierung über API v3 durchführen](user/picking-api.md)
- [Packprozess über API v3 durchführen](user/packing-api.md)
- [Versandprozess über API v3 durchführen](user/shipping-api.md)
- [Ladelisten über API v3 bearbeiten](user/loading-api.md)
- [Statusmeldungen über die Outbox übernehmen](user/integration-outbox.md)
- [Bestände über API v3 umbuchen](user/inventory-movement-api.md)
- [ERP-System anbinden](user/erp-integration.md)

## Verbindliche Dokumentationsregel

Jeder neue Slice und jede neue Phase aktualisiert im selben Commit:

1. mindestens eine Seite unter `docs/technical/`;
2. mindestens eine Seite unter `docs/user/`;
3. bei neuen Seiten dieses Inhaltsverzeichnis;
4. Migrationen, Berechtigungen, Zustandsregeln und bekannte Einschränkungen.

Eine Funktion gilt erst als abgeschlossen, wenn Implementierung, automatisierte
Tests sowie technische und fachliche Dokumentation gemeinsam vorliegen.
