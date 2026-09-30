# Zielstruktur des modularen Monolithen

## Ausgangslage

WebWMS enthält derzeit zwei parallel verwendete Organisationsmodelle:

- die klassische Symfony-Struktur mit zentralen Ordnern wie `Controller`,
  `Entity`, `Repository`, `Service` und `Form`;
- die für WebWMS 3.0 eingeführten Fachmodule mit den Schichten `Domain`,
  `Application` und `Infrastructure`.

Zusätzlich liegen Web- und API-Controller sowie Twig-Templates außerhalb der
jeweiligen Fachmodule. Dadurch sind fachlich zusammengehörige Dateien über
mehrere Verzeichnisse verteilt. Die Zielstruktur führt diese Bestandteile pro
Fachbereich zusammen. Der Begriff `V3` bezeichnet weiterhin die öffentliche
API-Version, ist aber kein Bestandteil interner Klassen- oder Ordnernamen.

## Leitprinzip

Die oberste Verzeichnisebene beschreibt einen Fachbereich. Innerhalb eines
Fachbereichs werden größere Teilbereiche nach fachlichen Fähigkeiten gegliedert.
Erst darunter folgen die technischen Schichten.

```text
src/
├── Administration/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
│       ├── Web/
│       └── Api/
├── Warehouse/
│   ├── Topology/
│   ├── Stock/
│   ├── InventoryCount/
│   └── InternalTransport/
├── Inbound/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
├── Outbound/
│   ├── Picking/
│   ├── Packing/
│   ├── Shipping/
│   └── Loading/
├── Integration/
│   ├── Erp/
│   ├── Carrier/
│   ├── Wcs/
│   ├── Printing/
│   ├── Outbox/
│   └── Devices/
├── Platform/
└── Shared/
    ├── Domain/
    ├── Application/
    └── Infrastructure/
```

Kleine Fachbereiche dürfen zunächst direkt in `Domain`, `Application`,
`Infrastructure` und `Presentation` gegliedert werden. Zusätzliche
Unterbereiche werden erst gebildet, wenn dadurch mehrere zusammengehörige
Anwendungsfälle und Modelle gemeinsam auffindbar werden.

## Bedeutung der Schichten

### Domain

`Domain` enthält Geschäftsregeln, Aggregate, Value Objects, fachliche Events
und die Schnittstellen zu benötigten Repositories. Domain-Code kennt weder
Symfony noch Controller, Twig, Doctrine DBAL oder konkrete Transportprotokolle.

### Application

`Application` enthält die Anwendungsfälle des Moduls. Commands, Queries und
Handler koordinieren Domain-Objekte und Ports, enthalten aber keine
HTTP-spezifische Logik. Lese- und Schreibmodelle dürfen getrennt behandelt
werden.

### Infrastructure

`Infrastructure` enthält technische Adapter, beispielsweise DBAL- oder
Doctrine-Repositories, Messenger-Handler, HTTP-Clients und Implementierungen
externer Schnittstellen. Eine Infrastrukturklasse implementiert einen Port aus
`Domain` oder `Application`.

### Presentation

`Presentation` enthält die Ein- und Ausgänge der Anwendung:

- `Presentation/Web` für HTML-Controller und Formulare;
- `Presentation/Api` für versionierte HTTP-APIs;
- bei Bedarf `Presentation/Console` für Konsolenbefehle.

Controller validieren Transportdaten, rufen genau definierte Anwendungsfälle
auf und erzeugen die passende Response. Geschäftslogik gehört nicht in diese
Schicht.

## Beispiel: Lagertopologie

```text
src/Warehouse/Topology/
├── Domain/
│   ├── StorageBin.php
│   ├── Warehouse.php
│   └── WarehouseTopologyRepository.php
├── Application/
│   ├── CreateStorageBin.php
│   ├── UpdateWarehouse.php
│   └── WarehouseTopologyQuery.php
├── Infrastructure/
│   └── Persistence/DbalWarehouseTopologyRepository.php
└── Presentation/
    ├── Web/WarehouseTopologyController.php
    └── Api/WarehouseTopologyApiController.php
```

Die dazugehörigen Templates spiegeln die fachliche Struktur wider:

```text
templates/
├── administration/
├── warehouse/
│   ├── topology/
│   ├── stock/
│   └── inventory_count/
├── inbound/
├── outbound/
└── integration/
```

## Namespace- und Namensregeln

Der Dateipfad und der PHP-Namespace müssen übereinstimmen. Beispiele:

| Verantwortung | Namespace |
| --- | --- |
| HTML-Controller der Lagertopologie | `WebWMS\Warehouse\Topology\Presentation\Web` |
| API-Controller der Lagertopologie | `WebWMS\Warehouse\Topology\Presentation\Api` |
| Anwendungsfall der Lagertopologie | `WebWMS\Warehouse\Topology\Application` |
| DBAL-Adapter der Lagertopologie | `WebWMS\Warehouse\Topology\Infrastructure\Persistence` |

Interne Klassen tragen keine Versionsnummer. Versionsangaben bleiben auf
externen Verträgen, insbesondere auf dem URL-Präfix `/v3`, API-Routen und der
API-Dokumentation. Dadurch kann die interne Implementierung weiterentwickelt
werden, ohne Klassen bei jeder API-Version umzubenennen.

## Abhängigkeitsregeln

1. `Domain` hängt von keiner anderen technischen Schicht ab.
2. `Application` darf das eigene `Domain` und veröffentlichte Verträge anderer
   Module verwenden.
3. `Infrastructure` implementiert Ports aus `Domain` oder `Application`.
4. `Presentation` verwendet Anwendungsfälle, aber keine konkreten
   Persistenzadapter.
5. Ein Modul schreibt nicht direkt in Tabellen, die einem anderen Modul
   gehören.
6. Modulübergreifende Abläufe verwenden explizite Verträge oder Events.
7. `Shared` enthält nur wirklich fachbereichsübergreifende Bausteine und darf
   nicht als allgemeiner Ablageordner verwendet werden.

## Migrationsstrategie

Die Umstellung erfolgt fachbereichsweise und nicht als einmalige
Großmigration. Ein Slice verschiebt Controller, Anwendungslogik, Adapter,
Templates und Tests eines zusammenhängenden Bereichs gemeinsam.

Während der Migration gelten folgende Kompatibilitätsregeln:

- bestehende `/v3`-URLs und Routennamen bleiben zunächst stabil;
- Datenbanktabellen und Migrationen werden durch reine Struktur-Slices nicht
  verändert;
- Service-IDs werden nur dann explizit konfiguriert, wenn Autowiring nicht
  ausreicht;
- alte Namespaces werden nach einem abgeschlossenen Slice nicht als dauerhafte
  Parallelstruktur weitergeführt;
- jeder Slice muss mit ECS, PHPStan und PHPUnit geprüft werden.

Die geplante Reihenfolge ist:

1. Namespace-Regeln und Zielstruktur dokumentieren;
2. Warehouse und Lagertopologie migrieren;
3. Administration migrieren;
4. Inbound migrieren;
5. Outbound einschließlich Picking, Packing, Shipping und Loading migrieren;
6. Integration und Plattform migrieren;
7. verbleibende Legacy-Struktur bereinigen und Architekturregeln automatisiert
   prüfen.

## Abgrenzung zur alten Symfony-Struktur

Die Ordner `Controller`, `Service` oder `Repository` sind bei kleinen
Anwendungen übersichtlich, verlieren bei vielen hundert Klassen aber den
fachlichen Zusammenhang. Die neue Struktur übernimmt daher nicht die Struktur
vor WebWMS 3.0. Sie behält die fachlichen Modulgrenzen bei und verbessert deren
konsequente Umsetzung, indem auch Präsentation, Templates und Tests dem
jeweiligen Fachbereich zugeordnet werden.
