# Vorgeschlagenes fachliches Datenmodell

| Bounded Context | Kern-Entities | Verantwortung |
| --- | --- | --- |
| Organisation | Tenant, BusinessPartner, Site, Warehouse | Mandantenfähigkeit, Standorte und Partner |
| Lagerstruktur | WarehouseArea, Aisle, StorageBin, ProcessStation | Topologie und physische Prozesspunkte |
| Artikel | Product, Barcode, Batch, SerialNumber, ExpiryDate | Stammdaten und Rückverfolgung |
| Bestand | StockItem, StockBalance, StockStatus, StockMovement | Bestandswahrheit und Ledger |
| Ladeeinheiten | HandlingUnit, LoadCarrier, Package | Palette, Behälter und Paket |
| Eingang | PurchaseOrder, InboundDelivery, GoodsReceipt, QualityCheck | Beschaffung bis Einlagerung |
| Ausgang | OutboundOrder, Reservation, PickOrder, PackingSession, Shipment | Auftrag bis Versand |
| Transport | TransportOrder, TransportTask, ReplenishmentTask | Interne Bewegungen |
| Inventur | InventoryCount, CountLine, CountDifference, Adjustment | Zählung und Korrektur |
| Integration | IntegrationMessage, OutboxMessage, ImportJob, ApiClient | Zuverlässiger Datenaustausch |
| Automatisierung | DomainEvent, AutomationRule, PrintJob | Ereignisbasierte Folgeaktionen |
