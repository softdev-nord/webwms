# Coglas-Helpcenter-Gap-Analyse vom 24.09.2026

## Ziel und Methode

Analysiert wurden die öffentlich erreichbaren Seiten [COGLAS Prozesse](https://help.coglas.com/ger/coglas-prozesse), [Prozesse & Konzepte](https://help.coglas.com/ger/prozesse-konzepte), [COGLAS Menü](https://help.coglas.com/ger/coglas-menu), der [Changelog](https://help.coglas.com/ger/changelog) sowie deren verlinkte Unterseiten. Der Abgleich erfolgte gegen Migrationen, Application Services, V3-Web-/API-Controller, Templates, Tests und die Ticketnachweise im Repository.

Die Bewertung bedeutet:

- **Abgedeckt:** belastbarer fachlicher Kern samt bedienbarer Oberfläche/API ist nachweisbar.
- **Teilweise:** ein verwandter Kern existiert, wesentliche Coglas-Konfigurationen oder Prozessvarianten fehlen.
- **Fehlt:** keine belastbare Umsetzung im WebWMS-3.0-Code nachweisbar.
- **Nicht Ziel:** Coglas-spezifische Technologieentscheidungen oder erst als „in Planung“ bezeichnete Funktionen sind keine zwingende Paritätsanforderung.

## Management Summary

Die bisherigen 96 Stories bilden die zentralen Prozessketten von Wareneingang, Bestand, Kommissionierung, Versand, Administration und Integration ab. Eine vollständige Funktionsparität mit dem aktuellen Coglas-Helpcenter besteht dennoch nicht. Der Helpcenter-Abgleich hat vor allem in den Bereichen konfigurierbare Stammdaten, Lageroptimierung, Security-Betrieb, dokumentenweiter Druck, Produktion/Montage, Billing sowie Außenhandel zusätzliche Anforderungen ergeben.

Für die bislang nicht ausreichend abgedeckten Funktionen wurden `WEBWMS-097` bis `WEBWMS-110` im neuen Epic `WEBWMS-EPIC-PARITY` formuliert. Bestehende Integrationsstories `WEBWMS-085` bis `WEBWMS-096` bleiben zusätzlich offen und werden durch die Detailanforderungen aus dieser Analyse präzisiert.

## Funktionsvergleich

| Coglas-Bereich | Coglas-Funktionen und Konfigurationen | WebWMS-Nachweis | Bewertung | Lücke / Folgemaßnahme |
| --- | --- | --- | --- | --- |
| Stammdaten | Artikel, Geschäftspartner, Prozesshinweise, Übersetzungen, Alternativartikel | Produktreferenzen, Partner, Stücklisten | Teilweise | Barcodekonfiguration, echte Mengeneinheiten/Umrechnung, Gebinde, Übersetzungen und Alternativartikel fehlen → `WEBWMS-097` |
| Behälter/LHM | LHM-Typen, LHM-Konto, Paletten und geschlossener nummerierter Behälterkreislauf | LHM-Konto und Kontobewegungen | Teilweise | Individueller Behälterstatus, Verwendungshistorie und Barcode-Import fehlen → `WEBWMS-098` |
| Benutzer/Rollen | Benutzer, Rollen, Partnerportal, SSO | Benutzer/Rollen/Portal vorhanden; SSO-Backend vorhanden | Teilweise | SSO-UI bleibt `WEBWMS-079`; Kennwortrichtlinien, Login-Historie, Profil und Arbeitsstationssitzung fehlen → `WEBWMS-102` |
| Lagerstruktur | Mehrlager, Topologie, Zonen, Kapazität, gestapelte/mehrfachtiefe und temperaturgeführte Lagerung | Standort bis Fach, Kapazität und Belegung | Teilweise | Feld-/Fachlasten, Temperatur und mehrfachtiefe Zugriffsregeln fehlen → `WEBWMS-099` |
| Gefahrstoff | Artikelklassifizierung, Gefahrstoffbereiche, Zusammenlagerung, Gewichtsgrenzen | Gefahrklassen, Materialklassifizierung und Restriktionen | Teilweise | Bereichsgewicht, „nur Gefahrstoff“ und Zusammenlagerungsmatrix ergänzen → Änderung an `WEBWMS-026`/`WEBWMS-099` |
| Bestandsführung | Echtzeitbestand, Chargen, MHD, Seriennummern, Sonderbestand, Sperren, Bewegungen | Vertikal in `WEBWMS-017` bis `025` vorhanden | Abgedeckt | Detailparität bei LE-Verschachtelung und Maße/Gewicht über `WEBWMS-099` prüfen |
| Bestandsplanung | Mindestbestand, Nachschub, Vorholung, Bestellvorschlag, standortübergreifender Abgleich | Nachschub und Vorholung vorhanden | Teilweise | Lieferantenbezogener Bestellvorschlag und 1-n-Abgleich fehlen → `WEBWMS-101` |
| Lageroptimierung | ABC/XYZ, Slotting, Verdichtung, Reorganisations-Umbuchungen | Wegeoptimierung für Picks vorhanden | Fehlt | Eigenständige Bestandsanalyse und Reorganisation → `WEBWMS-100` |
| Verschrottung | Systemzone, irreversible Ausbuchung, automatische Quittierung | Allgemeine Status-/Bestandsbuchung vorhanden | Teilweise | Kontrollierter Vernichtungsprozess und Nachweis fehlen → `WEBWMS-101` |
| Leitstände/KPI | Leitstände für Pick, Transport, Inventur, Aufträge, Fehlmengen und Haltbarkeit | Fachleitstände, Dashboard/KPI und MHD-Ampel vorhanden | Weitgehend abgedeckt | Kombinierte Fehlmengen- und MHD-Eingriffsansicht als UI-Nacharbeit prüfen |
| Wareneingang | Avis, geplant/ungeplant, QS, Dekonsolidierung, Restmengen, EAN-128/GS1, Produktion, Retouren | `WEBWMS-001` bis `014` vollständig | Teilweise | Konfigurierbarer Barcodeparser, verschachtelte Felder und effektive Strategie fehlen → `WEBWMS-105` |
| Transport/Picking | Single-/Multi-/Wave-/zweistufig, mobil, Regeln, Vorholung, Staplerleitsystem | `WEBWMS-033` bis `048` vollständig | Teilweise | Pick by Vision/Light, zonenseriell, Mehrmenge, Set-Artikel und frei konfigurierbare Scanvalidierung → `WEBWMS-106` |
| Warenausgang | Auftrag, Packen, Scan/Label/Ship, Versand, Tour, Verladung, Dokumente | `WEBWMS-049` bis `065` vollständig | Teilweise | Direkt-/Automatikverpackung, Sortierung, Träger-LE, Packstückoptimierung und Sammelverzollung → `WEBWMS-107` |
| Dokumente/Druck | Objektweite Anhänge, Reports, Report-/Labeldesign, Arbeitsplatzrouting, Kopien | Medien, Versanddokumente, Druckjobs und Routing vorhanden | Teilweise | Objektweite Dokumentverwaltung, editierbare Vorlagen/Layoutversionen und Multi-Ausgabe fehlen → `WEBWMS-103` |
| Schnittstellen | JSON API, XML/XLSX/CSV, SAP, ERP, Shops, Carrier, Mapping, Adapter, Historie | API/Outbox und mehrere Adapterkerne vorhanden | Teilweise | Offene `WEBWMS-084` bis `096`; Mapping, Nachrichtentypen und Ende-zu-Ende-Historie → `WEBWMS-104` |
| Produktion/Montage | Stücklisten, Reihenfolge, Seriennummern, Aufträge, Versorgung/Entsorgung, Produktionseingang | Stückliste, Materialbedarf, Produktionseingang und Routenzug vorhanden | Teilweise | Zustandsautomat für Produktions-/Montageauftrag, Reihenfolge und Versorgung fehlen → `WEBWMS-108` |
| Billing & Contract | Tarife, Lagergeld, VAS, Werkvertrag, Rechnungen/Vorlagen, Kontierung, DATEV, ZUGFeRD | Tarifregeln und abrechenbare Positionen vorhanden | Teilweise | Rechtssichere Rechnung, Vorlagen, Steuern/Konten/Kostenstellen, DATEV und ZUGFeRD fehlen → `WEBWMS-109` |
| Administration | Nummernkreise, Prozesse, Lageroptionen, Eventcenter, Archivierung, Eingabewerte, Mail, MIME, Vorlagen | Nummernkreise, Prozesse, Eventregeln und Deploymentprofil vorhanden | Teilweise | Archivierungsjobs, dynamische Eingabewerte, SMTP OAuth2, technische Vorlagenverwaltung → `WEBWMS-102`, `103`, `104` |
| Compliance/Qualität | Sanktionslisten, Zoll/Konsignation/VMI/Warenwerte, Stichproben | Sperr-/QS-Prozesse und Sonderbestände vorhanden | Teilweise | Außenhandels- und Sanktionsprüfung sowie statistische Stichproben fehlen → `WEBWMS-110` |
| UI/Allgemein | Responsive UI, Mehrsprachigkeit, Infoscan, globale Suche, Bilder/Notizen | Responsive V3, Suche und Medien vorhanden | Teilweise | Übersetzbare Stammdaten und universeller Barcode-Infoscan fehlen → `WEBWMS-097`, `105` |

## Vollständige Untermenü-Inventur

Die folgende Verdichtung ordnet jeden auf der Seite „COGLAS Menü“ aufgeführten Untermenüpunkt ein. „Abgedeckt“ bedeutet nicht identische Bildschirmgestaltung, sondern nachgewiesene fachliche Prozessabdeckung.

| Menügruppe | Abgedeckt | Teilweise | Fehlt / Ticket |
| --- | --- | --- | --- |
| Stammdaten | Änderungshistorie, Checklistenvorlagen, Geschäftspartner, LHM-Konto, Seriennummern | Anhänge, Artikel, Ladehilfsmitteltypen, Stücklisten | Barcodekonfigurationen, geschlossener Behälterkreislauf, Mengeneinheiten → `097`, `098` |
| Benutzerverwaltung | Benutzer, Rollen, Partnerportal | Arbeitsstationen, Single Sign-On | Aktuelle Arbeitsstation, Bildschirmskalierung, Login-Historie, Login-Richtlinien, Mein Profil → `079`, `102` |
| Datenschnittstelle | Integrations-Outbox und API-Grundlagen | Schnittstellen, Protokollierung, Rückmeldung, Schnittstellenhistorie, Setup, StorageUnits, Shopify, Carrier-/Transportadapter | Universeller Import/Export, Listenaktion, Nachrichtentypkonfiguration, Reports, Servicekonfigurationen, konfigurierbares Excel-Mapping → `084`–`096`, `104` |
| Lagerstruktur | Lagerübersicht, Shopfloor, Topologie ändern, Zonenübersicht | Gefahrstoffbereich, Infoscan, Strategien | Lageroptimierung, Verschrottung/Vernichtung → `099`–`101` |
| Bestand | Bestandsbewegungen, Bestandszuordnung, Chargenverfolgung, Inventur, Inventuraufträge/-listen/-zählung, Nachschub, Sperrlisten, Umbuchungen, Vorholung, Haltbarkeitswarnung | ERP-Lagerort, interne Aufträge, LE-Details | Mindestbestandsvorschau mit Bestellung → `099`, `101`, bestehend `087` |
| Wareneingang | Allgemeiner/geplanter/ungeplanter Wareneingang, Bestellung, Lieferscheine, Retouren, Wareneingänge | Produktionsaufträge und Produktionseingänge | Konfigurierbarer Scan-/EAN-128-Prozess → `105`, `108` |
| Transport | Transportdialog, Artikel-/LE-/Bestandsumlagerung, Kommissionierung, Kommissionier-/Transportleitstand, zweistufige Kommissionierung, Transportregeln, Vorholungsbedarfe | – | Erweiterte Assistenzverfahren aus dem Premium-Prozesskatalog → `106` |
| Warenausgang | Warenausgänge, Auftragsvorschau, Fehlmengen, Kundenaufträge/-leitstand, Scan/Label/Ship, Sendungen, Verladung, Verpackung | Verpackung mit Scan | Direktverpackung, Sortierung, automatischer Warenausgang, automatische Verpackung → `107` |
| Abrechnung | – | Abrechnung, Dienstleistungen, Lagerdienstleistungen und einfache Tarife | Artikelabrechnungsgruppen, Kontierung und vollständiger Belegprozess → `109` |
| Billing & Contract | VAS-, Lagergeld- und Dienstleistungsgrundlagen | Regeln und abrechenbare Positionen | Rechnungen/Vorlagen, Kostenstellen, Sachkonten, Steuern, Sofortrechnung, Werkvertrag, ZUGFeRD → `109` |
| Administration | Event Center, KPI, Nummernkreise, Prozess-/Deploymentkonfiguration | Lageroptionen, PrintController, Lagertechnik | Archivierungsaufträge, dynamische Typen, Eingabewerte, Logdateien, Mailversand, MIME-Typen, Reportvorlagen, technische Spezifikationen, Vorlagen → `102`–`104` |
| Allgemein | Bilderfassung, erweiterte Suche | Anmerkungen/Hinweise über Medien/Audit | Sanktionslistenprüfung → `103`, `110` |
| Produktion und Montage | Stücklistenbasis und Produktionseingang | Materialbedarf und Routenzug | Einrichtung, Montagereihenfolge/Produktionsseriennummern, Montageaufträge, vollständige Produktionsversorgung/-transporte → `108` |

## Erkenntnisse aus dem Changelog

Die Releases 2.150.0 bis 2.155.0 bestätigen, dass mehrere zunächst wie Detailoptionen wirkende Punkte produktiv relevant sind:

- Barcodevalidierung kann auf GTIN, individuellen Barcode oder Lieferantenmaterialnummer begrenzt werden.
- Strategien berücksichtigen Gruppierungsnummern, Bestandsqualifikationen und Zwischenziele.
- Verpackung unterstützt Gewichte, Bestandszuordnungen, Träger-Lagereinheiten, Sortierung und automatische Aktionen.
- Billing erzeugt kombinierte Rechnungen, ZUGFeRD und DATEV-Ausgaben.
- SMTP unterstützt OAuth 2.0; Stammdaten und Reports besitzen Übersetzungen beziehungsweise zusätzliche Felder.
- Produktionsaufträge unterstützen Versorgung und das Fortsetzen einer Montage.
- Gründe für Bestandsbuchungen sind konfigurierbare Eingabewerte.

Diese Punkte sind in die Akzeptanzkriterien der neuen Tickets eingeflossen. Reine Fehlerkorrekturen oder kundenspezifische Carrier-Mappings wurden nicht als eigene Story dupliziert.

## Bewusste Abgrenzungen

- Die von Coglas genannte MongoDB-/3-Tier-Architektur ist eine Implementierungsentscheidung, keine fachliche Paritätsanforderung. WebWMS bleibt bei Symfony, Doctrine/DBAL und MariaDB.
- Pick by Light, Pick to Belt, Put to Light, Track & Trace und RFID sind im Helpcenter als „in Planung“ markiert. Sie werden in `WEBWMS-106` beziehungsweise den Integrationsausblick aufgenommen, aber nicht als Voraussetzung für die aktuelle Coglas-Parität gewertet.
- Einzelne benannte Spediteure und kundenspezifische Mappings gehören in konfigurierbare Carrier-/Adaptertickets statt in eigene Produktstories.

## Empfohlene Reihenfolge

1. `WEBWMS-097`, `102`, `104` als konfigurierbare Basis.
2. `WEBWMS-099`, `100`, `101` für die operative Lagerparität.
3. `WEBWMS-105`, `106`, `107` zur Prozessvertiefung.
4. `WEBWMS-108`, `109`, `110` für Produktion, 3PL-Abrechnung und Compliance.
5. `WEBWMS-103` parallel als gemeinsamer Dokument-/Report-Unterbau.

## Quellenstand

Abrufdatum: 24.09.2026. Maßgeblich waren insbesondere:

- https://help.coglas.com/ger/coglas-prozesse
- https://help.coglas.com/ger/basis-prozesse
- https://help.coglas.com/ger/premium-prozesse
- https://help.coglas.com/ger/coglas-menu
- https://help.coglas.com/ger/prozesse-konzepte
- https://help.coglas.com/ger/changelog
- https://help.coglas.com/ger/barcodekonfigurationen
- https://help.coglas.com/ger/mengeneinheiten
- https://help.coglas.com/ger/lageroptimierung
- https://help.coglas.com/ger/arbeitsstationen
- https://help.coglas.com/ger/login-richtlinien
- https://help.coglas.com/ger/gefahrstoffbereich
- https://help.coglas.com/ger/produktion-und-montage
- https://help.coglas.com/ger/billing-contract
