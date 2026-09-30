# Analyse der V3-Übersichtsseiten vom 23.09.2026

Alle V3-Templates wurden anhand ihrer Formulare und Routen überprüft.

## Ergebnis der Klassifizierung

- Reine Übersichten und Reports bleiben ohne Schreibformular.
- Bereits getrennte Module – unter anderem ERP, Carrier, Geräte, Druck, WCS, Outbound, Verladung und die meisten Integrationsseiten – behalten ihre vorhandenen `index`-, `new`- und `show`-Views.
- Operative Prozessdetails behalten kontextbezogene Aktionen auf der Detailseite.
- Vermischte Stammdatenübersichten im Inventory- und Administrationsmodul wurden auf das einheitliche Listen-/Create-/Edit-Muster umgestellt.

## Technische Nachweise

- `WarehouseController`
- `WarehouseTopologyService`
- `StockSelectionService`
- `SpecialStockService`
- `StockBlockingService`
- `AdministrationController`
- neue Form-, Detail- und Übersichts-Templates unter `templates/warehouse/`
- getrennte Übersichten und Bearbeitungsseiten unter `templates/administration/`
- Navigation unter `templates/v3/subsections/sidebar.html.twig`
