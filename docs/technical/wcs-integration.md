# WCS-, MFR- und Fördertechnik-Integration in V3

Der Slice bildet die bidirektionale Kopplung zwischen WebWMS und Warehouse-Control-Systemen, Materialflussrechnern oder Fördertechnik ab. Gerätespezifische Protokolle bleiben von den fachlichen Transportbefehlen und Statusmeldungen getrennt.

## Datenmodell und Zustände

- `wms_wcs_connection` speichert Systemtyp, HTTPS-Endpoint und ausschließlich die Referenz auf eine Credential-Umgebungsvariable.
- `wms_machine_command` bildet Transport, Routing oder Abbruch für eine Ladeeinheit ab. `(tenant_id, request_id)` verhindert doppelte Befehle.
- Die Befehlsfolge lautet `queued → dispatched → accepted → completed`. Von nicht terminalen Zuständen sind außerdem `failed` und `cancelled` erlaubt.
- `wms_machine_status` erfasst idempotente Rückmeldungen über `external_event_id`, optional mit Bezug zu einem Transportbefehl.
- Alle Schreibvorgänge validieren Mandant, Benutzer und referenzierte Ressourcen und speichern Auditzeitpunkte.

## Schnittstellen

Die V3-Oberfläche liegt unter `/v3/integration/wcs`. JSON-Endpunkte stehen unter `/api/v3/wcs-connections`, `/api/v3/machine-commands` und `/api/v3/machine-statuses` zur Verfügung. Die Berechtigungen sind in Lesen, Konfigurieren und Ausführen getrennt.

Der aktuelle Slice führt keine externen Netzwerkaufrufe aus. Transportadapter sollen Befehle später zuverlässig über die Integrations-Outbox zustellen, Credentials erst zur Laufzeit aus der Umgebung beziehen und externe Ereignis-IDs zur Deduplizierung verwenden.
