# Carrier und Geräteintegrationen

Unter **Integration & Technik** stehen getrennte Arbeitsbereiche für Carrier,
Scanner und MDE, Drucker, Messgeräte sowie Lagerlifte und Paternoster bereit.

- **Carrier-Verbindungen** verwalten Versandprodukte, Labels, Tracking und
  Manifestübergaben.
- **Scanner und MDE** erfassen prozessbezogene Scans für Wareneingang, Picking,
  Packing, Versand, Verladung und Inventur.
- **Druckwarteschlange** zeigt PDF- und ZPL-Aufträge, deren Versuche und Fehler.
  Fehlgeschlagene Aufträge können erneut ausgeführt werden.
- **Waagen und Volumenmessung** übernimmt Gewicht und Abmessungen in Pakete oder
  Artikel. Abgelehnte Messungen bleiben im Journal, verändern aber keine Daten.
- **Lagerlifte und Paternoster** ordnet Aufgaben einem Gerät und Lagerplatz zu
  und zeigt Versand sowie erfolgreiche oder fehlerhafte Rückmeldungen.

Zugangsdaten werden nie in der Oberfläche gespeichert. Konfiguriert wird nur
der Name der Umgebungsvariable, die das Secret zur Laufzeit bereitstellt.
Wiederholte Requests mit derselben Request-ID erzeugen keine doppelten Vorgänge.
