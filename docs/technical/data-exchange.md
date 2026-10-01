# Integrationsgrundlage für Daten-, SAP- und Commerce-Austausch

Der Integrationskern verarbeitet mandantengebundene Importe und Exporte in
JSON, XML, CSV und XLSX. Jeder Lauf wird als `wms_integration_job` mit Format,
Ressourcentyp, Datensatzanzahl, Benutzer und Zeitpunkt protokolliert.

SAP-IDocs werden als XML angenommen. Der Kontrollsatz `EDI_DC40` liefert
Nachrichtentyp (`MESTYP`) und Belegnummer (`DOCNUM`); fehlende Pflichtwerte
führen vor der Persistierung zu einem Validierungsfehler. Feldzuordnungen sind
für ERP, SAP-IDoc und Commerce konfigurierbar und enthalten eine explizite
Transformation.

Commerce-Verbindungen speichern nur die Referenz auf ein Secret in der
Laufzeitumgebung. Kanalaufträge sind pro Mandant, Verbindung und externer
Auftragsnummer eindeutig. Verfügbarkeits- und Trackingdaten können über den
generischen Export und die bestehende Outbox bereitgestellt werden.

Die Endpunkte liegen unter `/api/v3/integration`; der formale Vertrag steht in
`docs/technical/openapi-v3.yaml` und ist über
`GET /api/v3/integration/openapi.yaml` abrufbar.
