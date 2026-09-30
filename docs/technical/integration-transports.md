# TCP/IP- und Webservice-Transporte in V3

## Modulstruktur

Die Integrations-Präsentation ist nach den Fähigkeiten `Automation`, `Carrier`,
`Device`, `Erp`, `Measurement`, `Outbox`, `Printing`, `Transport` und `Wcs`
unter `src/Integration` gegliedert. Web- und API-Controller liegen jeweils im
zugehörigen `Presentation`-Verzeichnis; die Views spiegeln diese Struktur unter
`templates/integration` wider.

Interne Web-Controller tragen keine Versionskennung mehr. Die öffentliche
Versionierung über `/v3` und `/api/v3` sowie sämtliche Routennamen bleiben
unverändert. Der derzeit noch bereichsübergreifende `ApiV3QueryService` wird in
einem separaten Query-Slice zerlegt, damit keine rein kosmetische Verschiebung
ohne klare Leseverträge entsteht.

Der Slice trennt fachliche Integrationen von ihrem technischen Transport. Ein `TransportEndpoint` beschreibt Adresse und Adapter, während `ProtocolConfiguration` Protokoll, Framing und Timeouts festlegt.

## Unterstützte Konfigurationen

- TCP-Client mit `raw_tcp` und Framing `none`, `newline` oder `stx_etx`
- HTTPS-Webservice mit `rest_json` oder `soap_xml` und Framing `http`
- Verbindungs-Timeout zwischen 100 und 60.000 Millisekunden
- Lese-Timeout zwischen 100 und 300.000 Millisekunden

Credentials werden nicht in der Datenbank gespeichert. Der Endpunkt referenziert nur eine Umgebungsvariable, deren Wert ein späterer Laufzeitadapter auflöst. Alle Schreibzugriffe sind mandantenbezogen und speichern Benutzer sowie Zeitpunkt.

Die Oberfläche liegt unter `/v3/integration/transports`, die JSON-API unter `/api/v3/transport-endpoints`. Der aktuelle Slice konfiguriert Transporte; Socket-/HTTP-Ausführung und Retry-Verhalten werden später mit der Integrations-Outbox gekoppelt.
