# Carrier-Anbindung verwenden

Im V3-Arbeitsbereich unter `/v3/integration/carrier-connections` können
berechtigte Benutzer Carrier-Verbindungen anzeigen, anlegen, pausieren und
aktivieren. Die Detailansicht zeigt Konfiguration und Auditdaten, jedoch niemals
das Secret selbst.

1. Carrier-Verbindung mit Carrier-Code, HTTPS-Endpunkt und dem Namen der
   Secret-Umgebungsvariablen anlegen.
2. Verbindung aktivieren und über die Detailansicht die verfügbaren
   Versandprodukte live abrufen.
3. Für eine vorbereitete Sendung im Bereich **Versand** das Label beim Carrier
   anfordern. Der V3-Arbeitsbereich verwendet dafür einen stabilen,
   sendungsbezogenen Idempotenzschlüssel.
4. WebWMS übernimmt Trackingnummer und Labelreferenz automatisch in die Sendung.
5. Tracking bei Bedarf synchronisieren oder ein abgeschlossenes Lademanifest an
   den Carrier übergeben.

Bei einem Timeout darf dieselbe `requestId` erneut verwendet werden. WebWMS gibt
eine bereits gespeicherte erfolgreiche Antwort zurück und verhindert dadurch
eine doppelte Label- oder Manifestanlage beim Carrier.

Die bisherige manuelle Registrierung von Trackingnummer und Labelreferenz bleibt
für Carrier ohne Adapter und für lokale Tests verfügbar.

Benötigte Rechte:

- `integration.carrier_connection.read`
- `integration.carrier_connection.write`
- `integration.carrier.read`
- `integration.carrier.execute`
