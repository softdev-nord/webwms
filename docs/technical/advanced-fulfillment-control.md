# Erweiterte Kommissionier- und Transportsteuerung

Der Slice `WEBWMS-033` bis `WEBWMS-048` ergänzt die vorhandenen Pick-, Umlagerungs- und Nachschubprozesse um einen gemeinsamen operativen Leitstand.

## Kommissionierung

`AdvancedPickingService` bündelt Picklisten als Single-Order-, Multi-Order- oder zweistufige Welle. Jede Pickliste erhält einen eindeutigen Zielbehälter. Wellen werden geplant und explizit freigegeben; zweistufige Wellen enden erst nach der Konsolidierung aller vollständig gepickten Listen.

Die Wegeoptimierung sortiert offene Pickpositionen anhand von Bereich, Gang, Ebene und Fach neu. Die mobile Bestätigung prüft Lagerplatz, Artikel, Charge, Seriennummer und Menge, protokolliert auch abgelehnte Scans und gibt erst danach die bestehende Pickbestätigung frei.

Der Leitstand zeigt Auftrag, Welle, Behälter, Bearbeiter, Fortschritt und Fehlmengen mandantengetrennt an. Alle Zustandsänderungen werden in `wms_fulfillment_event` mit Benutzer und Zeitpunkt festgehalten.

## Interner Transport

`InternalTransportService` verwaltet Flurförderzeuge, Transportregeln, Prozessstationen, Fahrbefehle und Routenzüge. Fahrbefehle unterstützen Umlagerung, Nachschub, Vorholung, Materialfluss und Routenzug. Der Ablauf ist `open` → `assigned` → `started` → `completed`; ungültige Übergänge werden atomar abgewiesen.

Produktbezogene Fahrbefehle verwenden beim Abschluss den bestehenden `TransferStockHandler`. Dadurch gelten dieselben Bestands-, Mandanten- und Dimensionsprüfungen wie bei einer manuellen Umlagerung. Regeln ordnen Transporte anhand von Auslöser sowie Quell- und Zielpräfix zu. Eine Routenzugauslösung erzeugt für jedes benachbarte Stationspaar einen Fahrbefehl.

Nachschubrichtlinien und -aufträge bleiben in den vorhandenen Inventory-Anwendungsdiensten und werden im Transportleitstand lediglich orchestriert.

## Persistenz und Rechte

Migration `Version20260921230000` führt Pickwellen, Scanereignisse, Transportressourcen, Regeln, Fahrbefehle, Prozessstationen, Routenzüge und das Fulfillment-Ereignisjournal ein. Neue Rechte liegen in den Namensräumen `fulfillment.pick.*`, `fulfillment.transport.*` und `fulfillment.replenishment.*`. Bestehende Rollen mit Pick- bzw. Umlagerungsrecht erhalten beim Upgrade die passenden neuen Rechte.

Die Weboberflächen liegen unter `/v3/picking-control` und `/v3/internal-transport`. Die entsprechenden JSON-Ressourcen beginnen mit `/api/v3/fulfillment-control`.
