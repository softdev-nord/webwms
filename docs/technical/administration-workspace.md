# Administrations-Workspace

Der V3-Administrations-Workspace bündelt die Tickets WEBWMS-075 bis WEBWMS-083. Alle operativen Datensätze tragen eine `tenant_id`; Fremdschlüssel und Anwendungsservice prüfen zusätzlich, dass referenzierte Geschäftspartner, Benutzer und Rollen demselben Mandanten angehören.

## Datenmodell

- `wms_business_partner` und `wms_tenant_context` bilden Geschäftspartner und getrennte Datenräume ab.
- `wms_identity_provider` speichert OIDC-/SAML-Konfigurationen. Secrets werden nicht in der Datenbank gespeichert, sondern nur über den Namen einer Umgebungsvariable referenziert. `wms_external_identity` hält die spätere Benutzerzuordnung.
- `wms_number_range` vergibt Nummern transaktional mit `SELECT … FOR UPDATE`; Präfix, Suffix, Stellenzahl, Obergrenze und optionales GS1-Unternehmenspräfix sind konfigurierbar.
- `wms_process_configuration` enthält mandantenbezogene Prozessschalter und validierte JSON-Konfiguration.
- `wms_device_profile` beschreibt Desktop-, Tablet- und Scannerprofile.
- `wms_deployment_configuration` dokumentiert SaaS-, On-Premises- oder Hybridbetrieb sowie Storage, Queue und Release-Kanal.
- `wms_administration_event` protokolliert relevante Änderungen mit Benutzer und Zeitpunkt.

## Schnittstellen und Sicherheit

Die Oberfläche liegt unter `/v3/administration/workspace`, die mandantensichere Projektion unter `/api/v3/administration/workspace`. Ressourcen können über `/api/v3/administration/workspace/{resource}` angelegt, Prozesse und Betriebsprofile per `PUT` konfiguriert und Nummern über `/api/v3/administration/number-ranges/{code}/next` atomar bezogen werden. Berechtigungen sind in `administration.configuration.read`, `administration.configuration.write` und `administration.number_range.use` getrennt.

OpenID-Connect- und SAML-Provider können bereits sicher konfiguriert werden. Der eigentliche Redirect-/Callback-Handshake bleibt providerabhängig und wird nach Auswahl der einzusetzenden IdP-Bibliothek ergänzt; WEBWMS-079 bleibt deshalb bewusst auf `Backend umgesetzt`.

## Webbetrieb

`manifest.webmanifest` und der Service Worker machen die V3-Oberfläche installierbar. Ausschließlich statische Assets werden offline zwischengespeichert; authentifizierte Seiten und API-Antworten werden aus Sicherheitsgründen nie gecacht.
