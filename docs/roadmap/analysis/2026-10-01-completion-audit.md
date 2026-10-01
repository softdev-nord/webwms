# Abschluss-Audit der WebWMS-3.0-Roadmap

**Stand:** 01.10.2026

> **Nachtrag vom 01.10.2026:** Nach diesem Abschlussstand wurde mit
> [WEBWMS-111](../tickets/WEBWMS-111.md) ein neuer, querschnittlicher Umfang
> für das vollständige Benutzerhandbuch aufgenommen. Der Audit dokumentiert
> weiterhin den Abschluss der zuvor definierten 110 Tickets; der aktuelle
> Roadmap-Stand beträgt damit 110 von 111 Tickets und 912 von 933 Story Points.
> WEBWMS-111 wurde anschließend vollständig umgesetzt. Der aktuelle Stand ist
> in der [Abschlussanalyse des Benutzerhandbuchs](2026-10-01-user-handbook-completion.md)
> dokumentiert.

## Ergebnis

Die fachliche WebWMS-3.0-Roadmap ist vollständig abgeschlossen. Sämtliche 110
Ticketdateien tragen den Status `Done`; ihre Story Points ergeben in Summe 912
von 912. Alle acht Epic-Dateien sind damit abgeschlossen.

| Epic | Tickets | Story Points | Status |
| --- | ---: | ---: | --- |
| Inbound | 14/14 | 85/85 | Done |
| Inventory | 18/18 | 126/126 | Done |
| Fulfillment | 16/16 | 132/132 | Done |
| Outbound | 17/17 | 106/106 | Done |
| Platform | 9/9 | 81/81 | Done |
| Administration | 9/9 | 76/76 | Done |
| Integration | 13/13 | 144/144 | Done |
| Functional Extensions | 14/14 | 162/162 | Done |
| **Gesamt** | **110/110** | **912/912** | **Done** |

WEBWMS-079 schließt dabei die letzte administrative Funktionslücke mit einem
vollständigen OpenID-Connect-Authorization-Code-Flow einschließlich Discovery,
State, Nonce, PKCE, JWKS-Signaturprüfung, mandantenbezogener
Identitätszuordnung, Session-Aufbau und Auditierung.

## Verbleibende Punkte außerhalb der Roadmap

Die folgenden Punkte sind keine offenen Akzeptanzkriterien der 110 Tickets,
sollten aber als eigene technische beziehungsweise betriebliche Folgeslices
geplant werden:

1. **Legacy-Code bereinigen:** Im klassischen Bestandsbuchungsbereich bestehen
   weiterhin leere `TODO`-Implementierungen, insbesondere unter
   `src/Event/Stock`, in `BookingMethodService` und
   `StockOutStrategyDataHandler`. Sie gehören nicht zum neuen modularen
   Anwendungspfad, erhöhen aber Wartungsrisiko und Missverständlichkeit.
2. **Strukturmigration abschließen:** Fünf Dateien liegen noch in den alten
   Verzeichnissen `src/Controller/Api/V3`, `src/Controller/Web` und
   `src/Security/V3`. Die öffentliche API darf weiterhin `/api/v3` heißen;
   interne Ordner und Namespaces sollen gemäß Zielarchitektur fachlich benannt
   sein.
3. **Technische Dokumentation konsolidieren:** Mehrere ältere Slice-Dokumente
   führen unter „Restarbeiten“ Funktionen auf, die durch spätere Tickets bereits
   umgesetzt wurden. Diese Aussagen müssen gegen den aktuellen Modulstand
   geprüft und entweder entfernt oder als echte technische Folgeaufgabe
   formuliert werden.
4. **Produktionsnahe Integration absichern:** Ergänzend zur grünen CI fehlen
   flächendeckende Tests gegen reale beziehungsweise containerisierte
   MariaDB-, Identity-Provider-, WCS-, Carrier- und Geräteendpunkte.
5. **Betrieb konfigurieren:** Für einen Produktivbetrieb sind
   umgebungsspezifische IdP-Registrierungen und Secrets, Messenger-Worker,
   Scheduler, Monitoring, Secret-Rotation und Rate-Limiting einzurichten und in
   der jeweiligen Betriebsumgebung nachzuweisen.

## Empfohlene Reihenfolge

1. veraltete technische Dokumentation konsolidieren;
2. produktionsnahe Integrations- und Ende-zu-Ende-Tests ergänzen;
3. die verbleibenden alten Namespaces verschieben;
4. ungenutzten Legacy-Code entfernen oder durch getestete Adapter ersetzen;
5. produktionsspezifische Betriebs- und Sicherheitskonfiguration abnehmen.

Damit ist die Roadmap fachlich geschlossen. Neue Arbeiten aus dieser Liste
sollten als technische Folge-Tickets mit eigenem Umfang und eigenen
Akzeptanzkriterien angelegt werden, statt abgeschlossene Roadmap-Tickets wieder
zu öffnen.
