# V3-Funktionsnavigation

## Ziel

Jede im V3-Frontend angebotene Funktion muss über eine berechtigungsabhängige Navigation und eine eigene erreichbare View verfügen. Detail- und Bearbeitungsseiten werden aus der jeweiligen Tabelle geöffnet; Anlage- und Erfassungsseiten erhalten zusätzlich einen direkten Untermenüpunkt, sofern sie ohne fachlichen Objektkontext aufrufbar sind.

## Navigation

- Plattform: Control Center und Partnerportal.
- Lager, Wareneingang und Warenausgang: fachliche Übersichten sowie kontextfreie Anlageviews.
- Integration & Technik: Übersichten, Verbindungs-, Geräte-, Mess-, Scan-, Druck- und Befehlsviews.
- Administration: Benutzer, Rollen, API-Clients und Mandantenkonfiguration inklusive Anlageviews.
- Funktionsparität: Gesamtübersicht, 18 getrennte Konfigurationsübersichten und 13 getrennte Prozessübersichten.

Die Sidebar prüft für jeden Eintrag dieselbe fachliche Berechtigung wie der zugehörige Controller. Detailseiten bleiben bewusst kontextbezogen und werden aus Tabellenzeilen verlinkt.

## Paritätsviews

`V3GapClosureController` stellt neben der Gesamtübersicht zwei parametrisierte Listentypen bereit:

- `v3_parity_configuration_index` filtert mandantensicher nach Konfigurationsressource.
- `v3_parity_workflow_index` filtert mandantensicher nach Workflowtyp.

Unbekannte Ressourcen oder Workflows werden abgewiesen. Anlage und Bearbeitung verwenden weiterhin getrennte Formularseiten. Alle Schreiboperationen bleiben über `platform.parity.write` beziehungsweise `platform.parity.execute` geschützt.

## Prüfregel

Beim Ergänzen einer V3-Funktion sind Controllerroute, Twig-View, Sidebar-Eintrag, Berechtigung und Tabellenaktion gemeinsam zu prüfen. Eine GET-Route darf nur dann ohne direkten Menüpunkt bleiben, wenn sie eine objektbezogene Detail-, Bearbeitungs- oder einmalige Credential-Ansicht ist.
