# Administrations-Workspace

Der V3-Administrations-Workspace bündelt die Tickets WEBWMS-075 bis WEBWMS-083. Alle operativen Datensätze tragen eine `tenant_id`; Fremdschlüssel und Anwendungsservice prüfen zusätzlich, dass referenzierte Geschäftspartner, Benutzer und Rollen demselben Mandanten angehören.

Die Präsentationsschicht ist fachlich unter `src/Administration/Presentation`
eingeordnet; ihre Twig-Views liegen unter `templates/administration`. Die
Anwendungsdienste und der Berechtigungskatalog tragen keine interne
Versionskennung mehr. Die öffentlichen `/v3`- und `/api/v3`-Verträge bleiben
davon unberührt.

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

OpenID-Connect-Provider können sicher konfiguriert und produktiv für die V3-Anmeldung verwendet werden. Der Authorization-Code-Flow lädt die Discovery-Metadaten ausschließlich per HTTPS, verwendet State, Nonce und PKCE S256 und tauscht den Code mit dem nur aus der Laufzeitumgebung gelesenen Client-Secret aus. Das ID Token muss RS256-signiert sein; Schlüsselrotation wird über `jwks_uri` und `kid` unterstützt. Issuer, Audience, Ablaufzeit, Nonce, Subject und verifizierte E-Mail werden geprüft.

Beim ersten erfolgreichen Login wird das externe Subject anhand der verifizierten E-Mail einem bereits aktiven Benutzer desselben Mandanten zugeordnet. Die Zuordnung und das Login werden auditierbar gespeichert. Spätere Logins verwenden ausschließlich die feste Subject-Zuordnung. SAML wird nicht als auswählbares Laufzeitprotokoll angeboten; dadurch existiert kein scheinbar konfigurierbarer, aber nicht ausführbarer Anmeldeweg.

## Webbetrieb

`manifest.webmanifest` und der Service Worker machen die V3-Oberfläche installierbar. Ausschließlich statische Assets werden offline zwischengespeichert; authentifizierte Seiten und API-Antworten werden aus Sicherheitsgründen nie gecacht.
