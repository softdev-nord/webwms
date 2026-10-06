# V3-Lagerarbeitsbereich

Der Lagerarbeitsbereich schließt WEBWMS-015 bis WEBWMS-018 als gemeinsamen vertikalen Slice ab.

## Topologie

`WarehouseTopologyService` verwaltet die mandantenbezogene Hierarchie Standort → Lager → Bereich → Gang → Ebene/Fach. Jeder schreibende Vorgang validiert die Elternbeziehungen und speichert den ausführenden Benutzer sowie den Zeitpunkt. `StorageBinDefinition` schützt Codes, Platztypen und Kapazitäten als Domain-Regeln.

Die Funktion ist nach der fachlichen Zielstruktur unter
`src/Warehouse/Topology` organisiert. Web- und API-Controller liegen gemeinsam
unter `src/Warehouse/Presentation`, die Twig-Views unter
`templates/warehouse`. Interne Klassen tragen keine Versionsnummer; die
öffentlichen `/v3`- und `/api/v3`-Verträge bleiben unverändert.

Die Migration `Version20260921090000` ergänzt Bereiche und Gänge sowie Topologiemetadaten an Lager und Lagerplatz. Bestehende Plätze bleiben durch nullable Beziehungen und kompatible Standardwerte gültig.

## Projektionen

- `warehouseTopology()` liefert die vollständige Struktur für UI und API.
- `warehouseOverview()` aggregiert Platzanzahl, belegte Plätze, Bestand und Kapazität über korrelierte Unterabfragen, sodass mehrere Bestandsdimensionen auf einem Platz die Platzkapazität nicht mehrfach zählen.
- `warehouseOccupancy()` projiziert jeden Lagerplatz mit Bereich, Gang, Ebene, Fach, Kapazität, Artikelanzahl, Bestand und Statusverteilung. Die optionale Lager-ID wird zusätzlich zum zwingenden Mandantenfilter angewendet.
- `stock()` liefert Echtzeitbestand einschließlich Lager, Bereich, Gang und Aktualisierungszeitpunkt.
- `stockMovements()` liefert das unveränderliche Ledger mit Benutzer, Grund, Dimensionen und resultierendem Bestand, neueste Bewegung zuerst.

Alle Queries filtern über `tenant_id`. Schreibzugriffe sind über `inventory.topology.write`, Lesezugriffe über `inventory.topology.read`, `inventory.overview.read`, `inventory.stock.read` und `inventory.stock.movement.read` getrennt autorisiert.

## Oberflächen und API

| Funktion | Web | API |
| --- | --- | --- |
| Topologie | `/v3/inventory/topology` | `/api/v3/inventory/topology` |
| Lagerübersicht | `/v3/inventory/overview` | `/api/v3/inventory/overview` |
| Grafische Lagebelegung | `/v3/inventory/occupancy` | `/api/v3/inventory/occupancy?warehouse={id}` |
| Echtzeitbestand | `/v3/inventory/stock` | `/api/v3/stock` |
| Bewegungshistorie | `/v3/inventory/movements` | `/api/v3/stock-movements` |

Topologieelemente werden über `POST /api/v3/inventory/topology/{sites|warehouses|areas|aisles|bins}` angelegt.

## Erweiterter Demodatensatz

Die frühere künstliche V3-Datengenerierung wurde entfernt. Die Installationsroutine verwendet stattdessen den normalisierten V2-Demodatenbestand als fachliche Quelle. Dabei wird die vollständige V2-Lagerstruktur in eigenständig adressierbare Lagerplätze überführt; V3-spezifische Konfigurationen werden anschließend gezielt auf dieser realistischen Grundlage ergänzt.

Die UUIDv5-IDs werden aus Mandant, Entität und laufender Nummer gebildet. Der Generator ist dadurch idempotent und kann nach Schemaänderungen oder zum Wiederauffüllen erneut ausgeführt werden. Mandanten, Benutzer und technische Integrationskonfigurationen bleiben bewusst bei ihren kompakten, manuell nachvollziehbaren Beispielen.
