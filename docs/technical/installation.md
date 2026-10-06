# WebWMS-Installationsroutine

## Zielbild

Die Webinstallation unter `/v3/install` führt durch Datenbank, Installationsumfang, Mandant, physischen Standort und das erste Administratorkonto. Die Datenbank muss vor dem Start existieren und vollständig leer sein. Der Installer besitzt absichtlich keine Berechtigung zum Erstellen oder Löschen einer Datenbank. Der bisherige Einstieg `/install` leitet auf die V3-Route weiter.

Ist `DATABASE_URL` bereits in der effektiven Prozessumgebung, `.env.local` oder `.env` gesetzt, übernimmt der Datenbank-Schritt Host, Port, Datenbankname, Benutzer, Kennwort und `serverVersion` automatisch. Bereits im Installationsassistenten geänderte Werte haben anschließend Vorrang, weil sie in der serverseitigen Installationssession gehalten werden.

Nach der Bestätigung führt WebWMS sämtliche Doctrine-Migrationen aus, legt die Erstdaten transaktional an und prüft Schema, Mandant, Standort, Administrator, Rolle und Berechtigungen. Erst nach bestandener Prüfung wird `var/installation.lock` geschrieben.

## Installationsvarianten

| Variante | Ergebnis |
|---|---|
| Ohne Demodaten | Aktuelles Datenbankschema, Mandant, Standort, Administratorrolle mit allen Berechtigungen und Administratorkonto |
| Mit Demodaten | Zusätzlich normalisierte V2-Stamm-, Auftrags-, Adress-, Bestands- und Topologiedaten sowie passende V3-Konfigurationen |

Die bisherige künstliche V3-Demodatengenerierung wurde vollständig entfernt. Fachliche Quelle ist `resources/installation/v2-demo-data.json`, eine normalisierte und UTF-8-kodierte Repräsentation des bereitgestellten V2-SQL-Exports. Die Installation übernimmt keine V2-Benutzer.

## Lagerstruktur und Standortbezug

Ein Lager gehört zu genau einem Standort. Bereiche, Lagerstrukturen und Lagerplätze gehören transitiv zu diesem Lager und damit zur physischen Standortstruktur. `tenant_id` bleibt in den Tabellen als Zugriffs- und Sicherheitsgrenze erhalten, ist aber nicht der fachliche Eigentümer der physischen Topologie.

Die Eindeutigkeit einer Lagerplatzkoordinate wird deshalb je Lager erzwungen. Jede Kombination aus Lagernummer, Fachboden, Stellplatz und Tiefe wird als eigener Datensatz in `wms_storage_location` erzeugt. Aus den 55 V2-Layoutdefinitionen entstehen 17.876 Plätze; weitere 50 tatsächlich vorhandene Plätze der nicht im Layout definierten Lagernummer 151 werden rekonstruiert. Der vollständige Demobestand umfasst damit 17.926 adressierbare Lagerplätze.

## Übernommene und ergänzte Daten

| V2-Quelle | V3-Ziel / Behandlung |
|---|---|
| `stock_layout`, `stock_location` | Standort → Lager → Bereich → Lagerstruktur → vollständiges Koordinatenraster |
| `stock_occupancy` | Bestandskonten und unveränderliches Eröffnungs-Ledger |
| `article` | Artikelstamm einschließlich Abmessungen und Gewicht |
| `customer` | Geschäftspartner und Empfängeradressen der Warenausgänge |
| `supplier` | Lieferanten und Absenderadressen der geplanten Wareneingänge |
| `supplier_orders`, Positionen | Bestellungen und geplante Wareneingänge |
| `customer_orders`, Positionen | Warenausgangsaufträge und Positionen |
| Nur V3 | Standard-Einlagerungsstrategie, FIFO-Entnahmeregel, Sperrgrund, Nummernkreise, Mandantenkontext und mobiles Geräteprofil |

Nicht automatisch in aktive Transporte übernommen werden die V2-Transportdatensätze ohne Zielkoordinate. Sie sind fachlich unvollständig und würden sonst nicht ausführbare Transportaufträge erzeugen.

## Erforderliche View-Anpassungen

Die Topologieübersicht ist auf einen physischen Standort filterbar. Nach Auswahl werden nur dessen Lager, Bereiche, Lagerstrukturen und Lagerplätze geladen. Lagerplätze zeigen Standort, Lager, Bereich und Lagerstruktur lesbar an. Die Abfrage begrenzt die Tabellenansicht auf 5.000 Plätze; die vollständige Struktur bleibt in Datenbank, Suche, Import und Bearbeitung adressierbar. Dadurch lädt die Oberfläche nicht versehentlich alle 17.926 Plätze eines Demostandorts in ein einzelnes HTML-Dokument.

## Betriebshinweise

- Der Webserver benötigt Schreibrechte für `.env.local` und `var/installation.lock`.
- Eine vorhandene kompilierte `.env.local.php` wird vor den Migrationen nach `var/installation.env.local.php.backup` verschoben. Dadurch verwendet Symfony garantiert die vom Installer geschriebene Datenbankverbindung; der Cache kann nach der Installation erneut mit `composer dump-env prod` erzeugt werden.
- Bei einem Abbruch nach gestarteten Migrationen muss die für die Neuinstallation vorgesehene Datenbank geleert oder neu angelegt werden.
- Das Datenbankkennwort wird während des Assistenten vorübergehend in der serverseitigen Session gehalten, nach erfolgreicher Installation daraus entfernt und weder in der Ergebnisansicht noch in der Lockdatei ausgegeben. Dauerhaft wird es ausschließlich in `.env.local` gespeichert.
- Die Installationsroute wird nach erfolgreicher Einrichtung zur V3-Anmeldung umgeleitet.
