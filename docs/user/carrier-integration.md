# Carrier-Anbindung verwenden

1. Carrier-Verbindung mit Carrier-Code, HTTPS-Endpunkt und dem Namen der
   Secret-Umgebungsvariable anlegen.
2. Verbindung aktivieren und die verfügbaren Versandprodukte abrufen.
3. Für eine vorbereitete Sendung ein Label mit einer neuen `requestId` anfordern.
4. WebWMS übernimmt Trackingnummer und Labelreferenz automatisch in die Sendung.
5. Tracking bei Bedarf synchronisieren oder ein abgeschlossenes Lademanifest an
   den Carrier übergeben.

Bei einem Timeout darf dieselbe `requestId` erneut verwendet werden. WebWMS gibt
eine bereits gespeicherte erfolgreiche Antwort zurück und verhindert dadurch
eine doppelte Label- oder Manifestanlage beim Carrier.

Benötigte Rechte:

- `integration.carrier_connection.read`
- `integration.carrier_connection.write`
- `integration.carrier.read`
- `integration.carrier.execute`
