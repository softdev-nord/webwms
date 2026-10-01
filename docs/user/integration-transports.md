# TCP/IP- und Webservice-Endpunkte verwenden

Unter **Integration & Technik → TCP/IP und Webservice** können berechtigte Benutzer technische Verbindungen für Anlagen und Hardware verwalten.

1. **Endpunkt anlegen** wählen und einen eindeutigen Code vergeben.
2. Für einen TCP-Client eine Adresse wie `tcp://anlage.local:9100` oder verschlüsselt `tls://anlage.local:9100` sowie Raw TCP und das erwartete Framing wählen.
3. Für einen Webservice eine HTTPS-Adresse sowie REST/JSON oder SOAP/XML und HTTP-Framing wählen.
4. Nur den Namen der Credential-Umgebungsvariable eintragen; das Geheimnis selbst gehört nicht in WebWMS.
5. Connect- und Lese-Timeout festlegen und den Endpunkt aktivieren.

Unpassende Kombinationen aus Adapter, Protokoll und Framing werden abgelehnt. Pausierte Endpunkte bleiben nachvollziehbar, können aber nicht beliefert werden. API-Clients mit Schreibrecht können über `/api/v3/transport-endpoints/{endpointId}/deliveries` Test- oder Prozessnachrichten senden; die optionale `messageId` dient dem Zielsystem als Idempotenzschlüssel.
