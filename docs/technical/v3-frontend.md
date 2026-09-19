# V3-Frontend-Schnitt

Das V3-Frontend nutzt die vorhandene mandantenfähige Session-Firewall unter `/v3`. Die Anmeldung kombiniert Mandanten-ID und E-Mail zu einem eindeutigen Security-Identifier. Nach erfolgreicher Anmeldung wird auf das V3-Dashboard umgeleitet.

Web-Controller lesen den Mandanten ausschließlich aus dem authentifizierten `TenantPermissionUser`. Filterwerte aus Requests können den Mandanten daher nicht überschreiben. Berechtigungen werden über `IsGranted` und den vorhandenen Permission-Voter durchgesetzt.

Die JSON-API unter `/api/v3` bleibt davon getrennt: Sie ist zustandslos und verwendet weiterhin API Keys. Das serverseitig gerenderte Twig-Frontend ruft Query-Services direkt auf und gibt keinen API Key an den Browser aus.

Der Slice umfasst:

- `/v3/login`: E-Mail- und Passwort-Anmeldung innerhalb eines Mandanten
- `/v3`: operatives Dashboard mit Stamm-, Bestands- und Fulfillment-Zahlen
- `/v3/inventory/stock`: berechtigungsgeschützte Bestandsprojektion mit Lagerfilter
- `/v3/outbound/orders`: Auftragserfassung, Freigabe, Reservierung und Allokation
- `/v3/picking`: Picklisten-Zuweisung und mobile Pickbestätigung
- `/v3/packing`: Paketbildung und Abschluss vollständig gepackter Aufträge
- `/v3/shipping`: Sendung, Label/Tracking, Druckwarteschlange und direkte Übergabe
- `/v3/loading`: Tour-/Fahrzeugmanifest, kontrollierte Verladung und gemeinsamer Abschluss
- `/v3/administration`: Benutzer, Rollen, Berechtigungen und API-Clients
- `webwms:v3:demo-bootstrap`: reproduzierbare lokale Beispieldaten

Weitere Frontend-Slices können dieselbe Shell und dieselbe Controller-Grenze verwenden. Schreibaktionen sollen weiterhin über Application Commands laufen; Query-Services bleiben reine Leseprojektionen.
