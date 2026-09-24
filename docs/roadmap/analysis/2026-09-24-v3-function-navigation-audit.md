# Audit der V3-Funktionsnavigation

**Stand:** 24.09.2026  
**Umfang:** V3-Webcontroller, GET-Routen, Twig-Views, Sidebar und Berechtigungen

## Ergebnis

Alle 91 von V3-Webcontrollern gerenderten Twig-Templates sind vorhanden. Alle regulären V3-GET-Routen werden nach der Korrektur entweder direkt im Menü oder kontextbezogen aus einer Übersichts-/Detailseite verlinkt. Die einmalige Anzeige neu erzeugter API-Zugangsdaten bleibt absichtlich ohne Menüpunkt.

## Geschlossene Navigationslücken

| Bereich | Festgestellte Lücke | Umsetzung |
| --- | --- | --- |
| Plattform | Partnerportal nur über direkte URL erreichbar | Eigene Plattformgruppe mit Control Center und Partnerportal |
| Erweiterte Funktionen | 18 Konfigurationsarten und 13 Workflows nur auf einer Sammelseite | Je Funktion eigener Untermenüpunkt und gefilterte Übersichtsview |
| Wareneingang | Anlage eines ungeplanten Eingangs nicht direkt navigierbar | Berechtigter Untermenüpunkt zur Anlageview |
| Warenausgang | Auftrag und Verladung nur aus Übersichten anlegbar | Direkte Untermenüpunkte für beide Anlageviews |
| Integration | Mehrere vorhandene Anlage-, Scan-, Mess-, Druck- und Befehlsviews nicht im Menü | Berechtigungsabhängige Untermenüpunkte ergänzt |
| Administration | Benutzer-, Rollen- und API-Client-Anlage nicht direkt navigierbar | Separate Untermenüpunkte ergänzt |

## Bewusste Ausnahmen

Objektbezogene Detail- und Bearbeitungsrouten erhalten keinen globalen Menüpunkt, weil ihre erforderliche ID erst durch die Auswahl einer Tabellenzeile bestimmt wird. Dazu gehören beispielsweise Bestands-, Druckjob-, Geräte-, Pick-, Pack-, Versand- und Verladedetails. Die API-Client-Credential-View ist nur unmittelbar nach dem Erzeugen eines Secrets gültig und bleibt ebenfalls kontextbezogen.

## Technische Absicherung

- Ressourcen- und Workflowparameter werden gegen zentrale Kataloge validiert.
- Listenabfragen sind nach Mandant und Funktion gefiltert.
- Schreib- und Ausführungsaktionen bleiben getrennt berechtigt.
- Unbekannte Workflowtypen werden vor einem Datenbankzugriff abgewiesen.
- Sidebar-Berechtigungen wurden gegen `V3PermissionCatalog` abgeglichen.
