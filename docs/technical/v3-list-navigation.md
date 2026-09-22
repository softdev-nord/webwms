# Einheitliche Suche, Filterung und Paginierung

Alle tabellarischen V3-Ansichten werden zentral durch `public/assets/js/v3-table-tools.js` erweitert. Dadurch benötigen neue Übersichtsseiten keine eigene Implementierung für Standardsuche und Paginierung.

## Funktionsumfang

- Volltextsuche über alle fachlich durchsuchbaren Spalten;
- sortierbare Spalten;
- einstellbare Seitengröße mit 10, 25, 50 oder 100 Einträgen;
- Seitennavigation und Anzeige des sichtbaren Ergebnisbereichs;
- Auswahlfilter für semantische Spalten wie Status, Typ, Lager, Strategie, System, Protokoll und Ressource;
- Speicherung von Suche, Filter, Sortierung, aktueller Seite und Seitengröße für 30 Tage;
- deutsche Beschriftungen und responsive Darstellung im bestehenden V3-Layout.

Die Initialisierung erfasst Tabellen innerhalb von `.main-content`, sofern sie die Klasse `.table` besitzen. Bereits initialisierte DataTables werden nicht erneut verarbeitet. Tabellen können mit `data-table-tools="false"` ausgenommen werden; das wird beispielsweise für Tabellen innerhalb der gerenderten Benutzerdokumentation verwendet.

Leere Twig-Ergebniszeilen mit `colspan` werden vor der DataTables-Initialisierung in eine native Leermeldung überführt. Dadurch bleiben die bestehenden fachlichen Hinweise erhalten, ohne eine inkonsistente Spaltenzahl zu erzeugen.

## Zusammenspiel mit Backend-Filtern

Fachliche Serverfilter, beispielsweise Lager, Zeitraum oder Zustellstatus, bleiben bestehen und grenzen zuerst die geladene Ergebnismenge ein. Suche, Spaltenfilter und Paginierung arbeiten anschließend ohne weiteren Request auf diesem Ergebnis. Dadurch bleiben bestehende URLs und Query-Services kompatibel.

## Erweiterung

Ein neuer Auswahlfilter wird über den normalisierten Spaltentitel in `FILTERABLE_HEADERS` registriert. Aktionsspalten mit leerer Überschrift sind automatisch weder sortierbar noch durchsuchbar.
