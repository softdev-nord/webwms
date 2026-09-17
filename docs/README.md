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
- [Architekturübersicht](architecture/README.md)

## Anwenderdokumentation

- [WebWMS 3.0 Grundlagen](user/getting-started.md)
- [Mandanten und Standorte](user/tenants-and-sites.md)
- [Benutzer, Rollen, Rechte und Anmeldung](user/access-security.md)
- [Artikel, Lagerplätze und Bestände](user/inventory-core.md)
- [Bestandsstatus, Chargen, Seriennummern und MHD](user/inventory-stock-dimensions.md)
- [Bestand umlagern und Status ändern](user/inventory-transfers.md)

## Verbindliche Dokumentationsregel

Jeder neue Slice und jede neue Phase aktualisiert im selben Commit:

1. mindestens eine Seite unter `docs/technical/`;
2. mindestens eine Seite unter `docs/user/`;
3. bei neuen Seiten dieses Inhaltsverzeichnis;
4. Migrationen, Berechtigungen, Zustandsregeln und bekannte Einschränkungen.

Eine Funktion gilt erst als abgeschlossen, wenn Implementierung, automatisierte
Tests sowie technische und fachliche Dokumentation gemeinsam vorliegen.
