# WebWMS mit Demodaten installieren

## Voraussetzungen

Legen Sie auf dem MySQL- oder MariaDB-Server eine neue, vollständig leere Datenbank und einen Benutzer mit Schema- und Datenrechten für diese Datenbank an. WebWMS erstellt und löscht die Datenbank bewusst nicht selbst.

Der Webserver benötigt Schreibrechte für `.env.local` sowie das Verzeichnis `var/`.

## Geführte Installation

1. Öffnen Sie `/v3/install`.
2. Tragen Sie Host, Port, Datenbankname, Benutzer, Kennwort und Serverversion ein.
3. Wählen Sie **Schema und V2-basierte Demodaten**. Mit **Nur Datenbankschema und Ersteinrichtung** werden keine fachlichen Beispieldaten angelegt.
4. Erfassen Sie den Namen des Mandanten und den physischen Hauptstandort einschließlich Zeitzone.
5. Erfassen Sie Anzeigename, E-Mail-Adresse und ein mindestens zwölf Zeichen langes Kennwort für den ersten Administrator.
6. Prüfen Sie die Zusammenfassung und starten Sie die Installation.
7. Warten Sie, bis alle Bereitschaftsprüfungen erfolgreich abgeschlossen sind, und öffnen Sie anschließend die Anmeldung.

## Umfang der Demodaten

Die Demodaten stammen aus dem bereitgestellten V2-Datenbankexport. Sie enthalten dessen Artikel, Kunden, Lieferanten, Bestellungen, geplante Wareneingänge mit Absenderadressen, Warenausgänge mit Empfängeradressen, Bestände und die vollständige Lagerstruktur. Jede Kombination aus Lagernummer, Fachboden, Stellplatz und Tiefe ist ein eigenständig adressierbarer Lagerplatz.

Für Funktionen, die in V2 nicht vorhanden waren, ergänzt der Installer geeignete V3-Erstkonfigurationen: Einlagerungs- und Entnahmeregeln, Sperrgrund, Nummernkreise, Standardkontext und mobiles Geräteprofil. Die früheren künstlichen V3-Massendaten werden nicht mehr verwendet.

## Prüfung nach der Installation

1. Melden Sie sich mit der im Installer vergebenen E-Mail-Adresse und dem Kennwort an.
2. Öffnen Sie **Bestand → Lagertopologie** und wählen Sie den Hauptstandort.
3. Prüfen Sie Lager, Bereiche, Lagerstrukturen und exemplarische Koordinaten. Die Demoinstallation enthält 17.926 Lagerplätze.
4. Öffnen Sie **Wareneingang → Geplante Eingänge** und prüfen Sie die Absenderadressen.
5. Öffnen Sie die Warenausgangsaufträge und prüfen Sie die Empfängeradressen.

Die Installation legt keine Zugangsdaten im Repository ab. Das Datenbankkennwort befindet sich ausschließlich in der lokalen `.env.local`; die Installationssitzung wird nach erfolgreichem Abschluss bereinigt.
