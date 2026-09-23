# Vorgeschlagenes fachliches Datenmodell

| Bounded Context | Kern-Entities | Verantwortung |
| --- | --- | --- |
| Organisation | Tenant, BusinessPartner, Site, Warehouse | Mandantenfähigkeit, Standorte und Partner |
| Lagerstruktur | WarehouseArea, Aisle, StorageBin, ProcessStation | Topologie und physische Prozesspunkte |
| Artikel | Product, Barcode, Batch, SerialNumber, ExpiryDate | Stammdaten und Rückverfolgung |
| Bestand | StockItem, StockBalance, StockStatus, StockMovement | Bestandswahrheit und Ledger |
| Ladeeinheiten | HandlingUnit, LoadCarrier, Package | Palette, Behälter und Paket |
| Eingang | PurchaseOrder, InboundDelivery, InboundReceipt, UnplannedReceipt, QualityChecklist, InboundAttachment, PutawayOrder, CrossDockAssignment, ProductionReceipt, ReturnOrder | Beschaffung, Annahme, QS und Nachweis bis Einlagerung oder Direktbereitstellung |
| Ausgang | OutboundOrder, StockReservation, PickList, OutboundQualityCheck, PackingOrder, Package, Shipment, TrackingEvent, ShippingDocument | Bedarfsvorschau und Auftrag bis Tracking und Versanddokument |
| Transport | TransportOrder, TransportTask, ReplenishmentTask, TransportTour, TourStop, LoadingManifest, WeightConstraint | Interne Bewegungen, Tourplanung und Verladung |
| Inventur | InventoryCount, CountLine, CountDifference, Adjustment | Zählung und Korrektur |
| Integration | IntegrationMessage, OutboxMessage, ImportJob, ApiClient | Zuverlässiger Datenaustausch |
| Automatisierung | DomainEvent, AutomationRule, PrintJob | Ereignisbasierte Folgeaktionen |
