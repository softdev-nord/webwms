# WebWMS 3.0 Dokumentation

Die Dokumentation ist in technische Informationen für Entwicklung und Betrieb
sowie Anwenderanleitungen für die fachliche Nutzung getrennt.

## Technische Dokumentation

- [Phase 1: Runtime und Qualitätsbasis](technical/phase-1-runtime.md)
- [Phase 2: Mandanten und Standorte](technical/phase-2-administration.md)
- [Benutzer, Rollen, Rechte und Symfony Security](technical/access-security.md)
- [Inventory Core](technical/inventory-core.md)
- [Bestandsmerkmale: Status, Charge, Seriennummer und MHD](technical/inventory-stock-dimensions.md)
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
- [Architekturübersicht](architecture/README.md)

## Anwenderdokumentation

- [WebWMS 3.0 Grundlagen](user/getting-started.md)
- [Mandanten und Standorte](user/tenants-and-sites.md)
- [Benutzer, Rollen, Rechte und Anmeldung](user/access-security.md)
- [Artikel, Lagerplätze und Bestände](user/inventory-core.md)
- [Bestandsstatus, Chargen, Seriennummern und MHD](user/inventory-stock-dimensions.md)
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

## Verbindliche Dokumentationsregel

Jeder neue Slice und jede neue Phase aktualisiert im selben Commit:

1. mindestens eine Seite unter `docs/technical/`;
2. mindestens eine Seite unter `docs/user/`;
3. bei neuen Seiten dieses Inhaltsverzeichnis;
4. Migrationen, Berechtigungen, Zustandsregeln und bekannte Einschränkungen.

Eine Funktion gilt erst als abgeschlossen, wenn Implementierung, automatisierte
Tests sowie technische und fachliche Dokumentation gemeinsam vorliegen.
