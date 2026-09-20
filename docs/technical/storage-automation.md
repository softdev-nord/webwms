# Lagerautomation in V3

Der Lagerautomations-Slice bildet Lagerlifte, Paternoster und Logimat-Geräte als mandantenfähige Integrationsressourcen ab. Er trennt Gerätekonfiguration, fachlichen Gerätebefehl und technische Übertragung, damit herstellerspezifische Protokolle später hinter Adaptern ergänzt werden können.

## Datenmodell und Zustände

- `wms_automation_device` speichert Gerätecode, Typ, HTTPS-Endpoint und ausschließlich die Referenz auf eine Credential-Umgebungsvariable.
- `wms_device_command` verknüpft Gerät, Lagerplatz und Prozessreferenz. `(tenant_id, request_id)` macht das Einreihen idempotent.
- Befehle wechseln von `queued` nach `dispatched` oder `failed` und von `dispatched` nach `completed` oder `failed`. Terminale Zustände lassen keinen weiteren Wechsel zu.
- Erzeugung und Zustandswechsel speichern Benutzer und Zeitpunkt. Sämtliche Zugriffe sind auf den Mandanten begrenzt.

## Schnittstellen

Die V3-Weboberfläche liegt unter `/v3/integration/automation`. Die JSON-API stellt Geräte unter `/api/v3/automation-devices` und Befehle unter `/api/v3/automation-commands` bereit. Zugriffe benötigen die Berechtigungen `integration.automation.read`, `integration.automation.write` oder `integration.automation.execute`.

Der aktuelle Slice führt keine externen HTTP-Aufrufe aus. Ein nachfolgender Transportadapter soll `queued`-Befehle über die Integrations-Outbox zuverlässig zustellen, Credentials erst zur Laufzeit aus der Umgebung laden und Geräteantworten auf die erlaubten Zustandsübergänge abbilden.
