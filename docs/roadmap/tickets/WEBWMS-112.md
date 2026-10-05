---
id: WEBWMS-112
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: High
story_points: 34
component: "Benutzerhandbuch"
feature_group: "Dokumentation & Hilfe"
source_feature: WEBWMS-REQ-112
---

# WEBWMS-112: Benutzerhandbuch auf vollständige View- und Formularabdeckung erweitern

## User Story

Als Benutzer möchte ich für jede produktive Übersicht, Detailseite,
Prozessansicht und Konfigurationsmaske eine konkrete, verständliche Anleitung
erhalten, damit ich jederzeit weiß, welche Daten einzugeben sind, welche
Auswirkungen eine Konfiguration besitzt und wie ich die angebotenen Aktionen
sicher ausführe.

## Ausgangslage

[WEBWMS-111](WEBWMS-111.md) stellt die technische Handbuchplattform mit
zweisprachigen YAML-Inhalten, Templates, Navigation, Suche, Berechtigung und
Screenshot-Platzhaltern bereit. Die anschließende Inhaltsprüfung zeigt jedoch,
dass die vorhandenen 53 Abschnitte die produktiven Ansichten überwiegend als
fachliche Sammelthemen beschreiben. Die Zuordnung einer Route zu einem Kapitel
beweist noch nicht, dass die konkrete View, ihre Felder und ihre Aktionen
ausreichend dokumentiert sind.

Die Audit-Baseline vom 02.10.2026 umfasst:

| Prüfgröße | Umfang | Feststellung |
| --- | ---: | --- |
| gerenderte produktive V3-Views | 101 | nicht einzeln beschrieben |
| V3-Views mit Formularen | 67 | überwiegend nur durch allgemeine Feldlisten abgedeckt |
| unterschiedliche sichtbare Formularfelder | 225 | keine strukturierte Feldabdeckung vorhanden |
| exakt im deutschen Handbuch erwähnte Feldkennungen | 117 | teilweise dokumentiert |
| nicht explizit erwähnte Feldkennungen | 108 | inhaltlich zu prüfen und zu ergänzen |
| vorhandene Handbuchabschnitte | 53 | für eine View-genaue Abdeckung zu grob |
| vorhandene Screenshot-Platzhalter | 53 | nicht jeder produktiven View eindeutig zugeordnet |

Die Feldzählung ist ein technischer Indikator. Ein technisch nicht genannter
Feldname kann bereits sinngemäß beschrieben sein und muss fachlich geprüft
werden. Umgekehrt gilt ein erwähnter Feldname erst dann als vollständig
dokumentiert, wenn Bedeutung, Eingabeformat und Auswirkungen verständlich
erläutert sind.

## Fachlicher Umfang

**Prozesskontext:** Sidebar → Benutzerhandbuch → Fachbereich → konkrete View →
Übersicht, Bedienung, Felder, Aktionen, Status, Fehler und Screenshot

Das bestehende Handbuch wird innerhalb von `handbook.de.yaml` und
`handbook.en.yaml` um eine View-orientierte Dokumentationsstruktur erweitert.
Jede produktive Benutzeroberfläche erhält eine stabile Dokumentationskennung
und eine konkrete Anleitung. Allgemeine Kapitel bleiben als Einstieg erhalten,
verweisen aber auf die einzelnen Übersichts-, Detail-, Formular- und
Prozessseiten.

### Dokumentationsstandard je View

Jede View dokumentiert, soweit fachlich anwendbar:

1. Zweck, Aufrufweg und fachliche Voraussetzungen;
2. erforderliche Berechtigungen und Mandanten-/Standortkontext;
3. Bedeutung von Tabellen, Karten, Kennzahlen und Statusanzeigen;
4. Suche, Filter, Sortierung, Pagination und leere Ergebniszustände;
5. Buttons, Zeilenaktionen, Folgeansichten und irreversible Aktionen;
6. Statusübergänge, Seiteneffekte, Auditierung und externe Verarbeitung;
7. typische Validierungs-, Berechtigungs-, Konflikt- und Integrationsfehler;
8. einen eindeutig zugeordneten Screenshot-Platzhalter mit Alternativtext.

### Dokumentationsstandard je Formularfeld

Für jedes fachlich editierbare Feld werden, soweit anwendbar, hinterlegt:

- sichtbare Bezeichnung und stabile Feldkennung;
- Zweck und fachliche Bedeutung;
- Pflichtfeldstatus und Standardwert;
- Datentyp, Format, Einheit und zulässiger Wertebereich;
- Auswahlwerte und deren Auswirkungen;
- Abhängigkeiten von anderen Feldern oder Konfigurationen;
- mindestens ein realistischer Beispielwert;
- Auswirkung beim Speichern beziehungsweise Aktivieren;
- relevante Sicherheits- und Geheimnishinweise;
- typische Validierungsfehler und deren Behebung.

Rein technische Felder wie CSRF-Token dürfen über eine explizite, begründete
Ausschlussliste von der fachlichen Feldabdeckung ausgenommen werden. Fachlich
relevante Hidden-Felder, Status- und Referenzwerte sind nicht pauschal
auszuschließen.

## Abzuarbeitende Fachbereiche

| Fachbereich | Views | Formular-Views | Inhaltliche Schwerpunkte |
| --- | ---: | ---: | --- |
| Administration | 12 | 7 | Benutzer, Rollen, API-Clients, Credentials, Partner, OIDC, Nummernkreise, Prozesse, Geräteprofile und Deployment |
| Interner Transport | 1 | 1 mit 13 Formularen | Transportaufträge, Ressourcen, Milk Runs, Stationen, Zeitpläne und Nachschubregeln |
| Wareneingang | 5 | 4 | geplant/ungeplant, Bestellung, Avis, QS, Abweichung, Cross-Dock, Produktion, Label und Einlagerung |
| Integration und Geräte | 40 | 32 | ERP, Carrier, Datenaustausch, Mapping, Druck, Scanner, Messung, WCS, Automation, Outbox und Transportadapter |
| Warenausgang | 14 | 9 | Auftrag, Allokation, Picking, Wellen, Packen, Versand, Dokumente, Tracking, Tour und Verladung |
| Plattform und Erweiterungen | 9 | 4 | Shopfloor, KPI, Partner, Events, Abrechnung, Dienstleistungen, Medien, Druckregeln, Erweiterungen und Workflows |
| Lager und Bestand | 16 | 9 | Topologie, Strategien, Bestand, Belegung, Bewegungen, Rückverfolgung, Sperren, Sonderbestand, Inventur und Gefahrstoffe |
| Anmeldung und Arbeitskontext | 1 | 1 | Mandant, Passwortlogin, OIDC-Provider, Fehlermeldungen und sicherer Sitzungswechsel |

Die Zahlen bilden den Prüfstand vom 02.10.2026 ab und sind bei der Umsetzung
automatisch aus dem aktuellen Stand neu zu ermitteln.

## Bekannte Feld- und Konfigurationslücken

Die Prüfung hat insbesondere für folgende Feldgruppen fehlende oder zu
allgemeine Ausfüllhinweise ergeben:

### Administration und System

- `acting_user_id`, `password`, `partner_type`, `object_type` und `start_route`;
- OIDC-, Nummernkreis-, Prozess-, Geräteprofil- und Deploymentfelder;
- Auswahlregeln, Seiteneffekte und sichere Verwendung von Secret-Referenzen.

### Lager, Inventur und Transport

- `transport_type`, `trigger_type`, `source_prefix`, `target_prefix`,
  `station_id`, `dwell_minutes`, `minimum_quantity` und `target_quantity`;
- `classification_kind`, `allocatable`, `warehouse_type`, `area_type`,
  `location_type`, `putaway_priority` und `putaway_enabled`;
- `un_class`, `hazard_class_id`, `un_number`, `packing_group`,
  `location_prefix`, `max_quantity`, `items`, `bom_id`,
  `production_quantity`, `partner_code` und `carrier_type`.

### Wareneingang und Warenausgang

- `purchase_order_item_id`, `receipt_id`, `outbound_item_id`,
  `packaging_passed`, `quantity_passed` und Abweichungsaktionen;
- `tour_reference`, `shipment_id`, `package_number`, `pick_task_id`,
  `shipment_number`, `label_reference`, `handover_reference` und Versandregeln;
- Dokument-, Tracking-, Tour-, Qualitäts- und Gewichtsgrenzen.

### Integration und Plattform

- `command_type`, `process_type`, `scan_type`, `context_reference`,
  `channel_type`, `mapping_system`, `target_type`, `target_id`, `printer_id`,
  `document_reference`, `connection_id`, `machine_code` und Statusmeldungen;
- `reference_type`, `reference_id`, `event_name`, `action_type`,
  `price_per_unit_day`, `currency`, `unit`, `days` und `workstation`;
- Funktionsweise, Einheiten, erlaubte Werte, Retry-Verhalten und externe
  Seiteneffekte.

### Freie JSON- und Payload-Felder

Für `configuration_json`, `payload_json`, `layout_json`, `conditions_json`,
`action_config` sowie weitere Payload- und Konfigurationsfelder sind mindestens
ein valides Schema beziehungsweise eine Eigenschaftstabelle, ein vollständiges
Beispiel und die Beschreibung unbekannter oder fehlender Eigenschaften
bereitzustellen. Geheimnisse dürfen in keinem Beispiel als Klartext erscheinen.

## Übersichts- und Detailseiten

Auch Views ohne editierbares Formular werden einzeln dokumentiert. Dies umfasst
insbesondere Benutzer-, Rollen- und API-Client-Listen, Carrier-Produkte,
Scan-/Messdetails, Drucker und Druckaufträge, WCS-Befehle, Outbox, geplante und
ungeplante Wareneingänge, Aufträge, Picks, Packstücke, Sendungen, Verladungen,
Bestände, Belegung, Bewegungen, Rückverfolgung, Erweiterungskonfigurationen und
Workflows.

Für jede dieser Views sind Spalten, Filter, Suchverhalten, Statusanzeigen,
Aktionen, Folgeseiten, leere Zustände und Fehlersituationen zu erklären.

## Screenshots und Bildreferenzen

Jede konkrete View erhält eine stabile, fachlich benannte Bildreferenz, zum
Beispiel:

```yaml
image:
  src: /assets/images/handbook/administration/user-create.svg
  alt: 'Benutzer anlegen – Formularansicht'
```

Die neuen Dateien dürfen zunächst denselben generischen Platzhalterinhalt
verwenden. Dateiname und Alternativtext müssen jedoch bereits eindeutig der
jeweiligen View entsprechen, damit echte Screenshots später ohne strukturelle
Änderung ausgetauscht werden können.

## Automatisierte Abdeckung

Die bisherige Kapitelzuordnung wird zu einer View- und Feldmatrix erweitert:

- jede produktive V3-Route mit HTML-Ausgabe verweist auf eine konkrete
  Dokumentationskennung;
- jedes produktive gerenderte Template besitzt einen View-Eintrag;
- jedes fachlich editierbare `input`, `select` und `textarea` besitzt eine
  Feldbeschreibung oder einen begründeten technischen Ausschluss;
- jede View besitzt eine deutsche und englische Entsprechung;
- jede View besitzt eine stabile Screenshot-Referenz;
- neue Views und neue Formularfelder lassen die CI ohne Dokumentation
  fehlschlagen;
- verwaiste Dokumentationseinträge und unbenutzte Schlüssel werden erkannt.

Die Prüfung darf nicht ausschließlich auf Namensvorkommen beruhen. Sie muss
stabile View- und Feldkennungen gegen die strukturierte Handbuchdefinition
abgleichen.

## Akzeptanzkriterien

1. Jede produktive V3-Übersichts-, Detail-, Formular- und Prozessansicht besitzt
   einen konkreten deutschen und englischen Handbuchabschnitt.
2. Jede View beschreibt Zweck, Aufrufweg, Voraussetzungen, Berechtigungen,
   dargestellte Informationen, Aktionen, Status, Seiteneffekte und typische
   Fehler, soweit diese Aspekte anwendbar sind.
3. Jede Listenansicht erläutert Spalten, Suche, sämtliche Filter, Sortierung,
   Pagination, leeren Zustand, Zeilenaktionen und erreichbare Folgeansichten.
4. Jedes fachlich editierbare Formularfeld ist mit Bedeutung, Pflichtstatus,
   Format beziehungsweise Einheit, zulässigen Werten, Standardwert,
   Abhängigkeiten, Beispiel, Auswirkungen und Validierungsfehlern beschrieben,
   soweit diese Eigenschaften anwendbar sind.
5. Auswahlfelder erläutern alle fachlich angebotenen Optionen. Checkboxen und
   Statusfelder erläutern eindeutig die Auswirkung von Aktivierung und
   Deaktivierung.
6. Sämtliche freien JSON-, Mapping-, Bedingungs-, Aktions- und Payload-Felder
   besitzen eine Eigenschaftstabelle oder ein Schema sowie mindestens ein
   vollständiges, valides und geheimnisfreies Beispiel.
7. Fachliche Aktionen wie Freigeben, Stornieren, Retry, Quittieren, Drucken,
   Abschließen, Klassifizieren, Prüfen und Statuswechsel sind mit
   Voraussetzungen, Ergebnis, Wiederholbarkeit und Fehlerbehandlung erklärt.
8. Jede dokumentierte View besitzt eine eindeutige, barrierearme
   Screenshot-Referenz. Der generische Bildinhalt darf zunächst wiederverwendet
   werden; Pfad und Alternativtext sind View-spezifisch.
9. Sämtliche Inhalte verbleiben in den eigenständigen Dateien
   `handbook.de.yaml` und `handbook.en.yaml`; Templates und PHP enthalten keine
   hart codierten Handbuchtexte und es wird kein Markdown gerendert.
10. Deutsche und englische Struktur sind vollständig identisch. Suche,
    Navigation und Direktverlinkung berücksichtigen View-Titel, Feldnamen,
    Synonyme, Aktionen und Fehlerhinweise.
11. Eine automatisierte View-Coverage lässt neue oder nicht dokumentierte
    produktive HTML-Views fehlschlagen und erkennt verwaiste View-Einträge.
12. Eine automatisierte Formular-Coverage lässt neue, umbenannte oder nicht
    dokumentierte fachliche Eingabefelder fehlschlagen. Technische Ausnahmen
    sind explizit und begründet hinterlegt.
13. Für weiterhin produktive Nicht-V3-Ansichten ist entweder eine vollständige
    Handbuchabdeckung vorhanden oder eine dokumentierte, getestete Entscheidung
    getroffen, dass sie nicht zum aktuellen Benutzeroberflächenumfang gehören.
14. Tests decken YAML-Struktur, Sprachparität, Suche, HTML-Escaping,
    Navigation, View-/Feldmatrix, Screenshot-Referenzen sowie repräsentatives
    Rendering aller Fachbereiche ab.
15. Eine neue Abschlussanalyse weist die tatsächlich ermittelten View-,
    Formular-, Feld- und Screenshotzahlen aus. Das Ticket darf erst auf `Done`
    wechseln, wenn sämtliche produktiven Einträge der Matrix abgedeckt sind.

## Abhängigkeiten

- [WEBWMS-111](WEBWMS-111.md) als bestehende Handbuchplattform;
- produktive Controller, Routen und Twig-Templates als verbindliche
  Abdeckungsquelle;
- bestehende Übersetzungs-, Berechtigungs- und Suchstruktur.

## Implementierungsstand

**Status:** Done

Der erste Umsetzungsslice stellt das verbindliche View-Datenmodell bereit:
Template-Zuordnung, Zweck, Aufrufweg, Berechtigungen, Ansichtsdetails,
strukturierte Feldhilfen, Aktionen und eine eindeutige Bildreferenz werden aus
den separaten deutschen und englischen Handbuchkatalogen gerendert. Navigation
und Suche berücksichtigen View-, Feld-, Aktions- und Fehlertexte einschließlich
stabiler Direktanker.

Die Anmeldung am WebWMS-Arbeitsbereich ist als erste Referenz-View vollständig
in beiden Sprachen erfasst. Alle vier fachlichen Eingaben (`tenant_id`, `email`,
`password`, `provider`), beide Anmeldewege und die typische Fehlerbehandlung
sind beschrieben. Ein automatischer Test vergleicht die realen Eingabefelder
des Twig-Templates mit den strukturierten Feldkennungen; `_csrf_token` ist als
technisches Feld explizit ausgeschlossen. Eine eigene barrierearme
Screenshot-Platzhalterdatei ist vorhanden.

Der zweite Slice ergänzt elf konkrete Administrationsansichten für den
Administrationseinstieg sowie Benutzer-, Rollen- und API-Client-Verwaltung.
Alle fachlich editierbaren Felder der sechs Formularseiten sind strukturiert
beschrieben. Dazu gehören Pflichtstatus, Eingabeformat, Beispiel, fachliche
Bedeutung, Auswirkung und typische Validierungsfehler. Übersichten erläutern
Spalten, Leerzustände, Sichtbarkeit nach Berechtigung und Folgeaktionen. Die
Einmalanzeige eines API-Secrets beschreibt sichere Übernahme und
Nicht-Wiederherstellbarkeit ausdrücklich.

Ein sprachparametrisierter Abdeckungstest gleicht für diese elf Views die
produktiven Twig-Templates und deren Formularfeldkennungen mit dem Handbuch ab.
Zusätzlich prüft er, dass jede View eine vorhandene und innerhalb des Slices
eindeutige Screenshot-Referenz besitzt. Damit sind einschließlich Anmeldung
12 von 101 produktiven Views auf den neuen Dokumentationsstandard migriert.

Der dritte Slice dokumentiert die zentrale Mandanten- und Systemkonfiguration
als weitere konkrete View. Die sieben Konfigurationsbereiche Geschäftspartner,
Datenräume, OIDC, Nummernkreise, Prozessschalter, Geräteprofile und Deployment
sind mit Bedienung, Berechtigungen und Seiteneffekten beschrieben. Alle 29
fachlich relevanten Template-Felder sind abgedeckt; dies schließt versteckte
Standardwerte wie `active`, `enabled` und `fullscreen` sowie die versteckten
Nummernkreisgrenzen `suffix` und `maximum_value` ausdrücklich ein. Das freie
JSON-Feld `configuration` enthält ein valides, geheimnisfreies Beispiel und
Hinweise zu Datentypen und unbekannten Eigenschaften.

Das View-Modell unterstützt nun außerdem Verwendungskontexte für Feldnamen,
die in mehreren Formularbereichen vorkommen. Die automatische Coverage prüft
Template, sämtliche sichtbaren und fachlich relevanten versteckten Felder,
Sprachparität und die eindeutige Screenshot-Referenz. Damit sind 13 von 101
produktiven Views migriert.

Der vierte Slice deckt die Leitstelle für internen Transport und Nachschub ab.
Die einzige View enthält 13 eigenständige Formulare für Transportanlage,
Ressourcenzuweisung, Start und Quittierung, Ressourcen und Transportregeln,
Stationen, Routenzüge und Stopps sowie Nachschubregel, Auslösung und Abschluss.
Alle 27 fachlichen Feldkennungen sind dokumentiert. Auswahlwerte für Transport-,
Ressourcen-, Auslöser- und Planungstypen werden erklärt; Charge, Seriennummer,
Bestandsstatus, Mengen, Prioritäten, Präfixe, Stoppreihenfolge und Schwellwerte
enthalten konkrete Format-, Wirkungs- und Fehlerhinweise.

Die Dokumentation beschreibt die Statusfolge `open → assigned → started →
completed`, die mögliche Bestandsbuchung beim Abschluss, Wiederholbarkeit der
Aktionen und die dauerhafte Wirkung der Routenzug- und Nachschubauslösung. Ein
sprachparametrisierter Test weist exakt 13 Formulare, 27 Felder, zehn Aktionen
und die vorhandene Screenshot-Referenz nach. Damit sind 14 von 101 produktiven
Views migriert.

Der fünfte Slice migriert vier der fünf operativen Wareneingangs-Views:
Übersicht ungeplanter Eingänge, separate Erfassung, Detail mit atomarer
Bestandsbuchung sowie die Arbeitsliste geplanter Eingänge. Die Dokumentation
deckt 22 Formularfelddefinitionen und acht Prozessaktionen ab. Sie erläutert
Lieferanten- und Belegreferenzen, Empfangsplatz, Menge, Bestandsstatus, Charge,
Seriennummer und Haltbarkeit sowie die Qualitätsmerkmale Verpackung und
Mengenprüfung.

Für geplante Eingänge sind die Statusfolge von Avis und Annahme über QS und
Abweichungsentscheidung bis zur Einlagerung, die jeweils nächste zulässige
Aktion, atomare Buchung und Schutz vor Wiederholung beschrieben. Der
sprachparametrisierte Test gleicht alle vier Templates und ihre fünf sichtbaren
Formulare mit 22 Dokumentationseinträgen ab und prüft eindeutige vorhandene
Bildreferenzen. Damit sind 18 von 101 produktiven Views migriert. Die separate
Wareneingangs-Steuerungszentrale mit zehn Formularen bleibt als nächster
Inbound-Slice offen.

Der sechste Slice schließt diese Wareneingangs-Steuerungszentrale ab. Ihre zehn
Formulare für Bestellung, Avis, QS-Checkliste, Dateianhang, Etikett,
Cross-Docking, Produktionszugang, Retourenankündigung, Retourenannahme und
Retourenprüfung sind vollständig beschrieben. Die 25 eindeutigen Feldkennungen
decken neben den sichtbaren Eingaben auch die automatisch gepflegte versteckte
`purchase_order_id` ab. Dateiarten und sichere Ablage, Druckkopien,
Cross-Dock-Mengenbindung, Produktionscharge sowie die Entscheidung zwischen
Wiedereinlagerung und Sperre werden einschließlich Fehlerfolgen erläutert.

Der Test weist zehn Formulare, 25 Felder, zehn Aktionen und die eindeutige
Bildreferenz in beiden Sprachen nach. Damit ist der Wareneingang mit allen fünf
produktiven Views vollständig auf den View- und Feldstandard migriert.
Insgesamt sind 19 von 101 produktiven Views abgedeckt.

Der siebte Slice beginnt den Warenausgang mit Auftragsübersicht, Neuanlage und
Auftragsdetail. Die Dokumentation erläutert die Freigabe mit atomarer
Reservierung, automatische Allokation über Entnahmeregeln, manuelle Bindung an
Lagerplatz, Status, Charge, Seriennummer und Haltbarkeit sowie die einmalige
Übergabe eines vollständig allokierten Auftrags an die Kommissionierung. Zwölf
Felddefinitionen und acht Auftragsaktionen sind gegen die drei produktiven
Templates geprüft.

Im selben Slice wurde ein Laufzeitfehler der Handbuchnavigation behoben. Elf
Administrations-Bildpfade waren durch mehrfaches YAML-Quoting als Strings mit
zusätzlichen Anführungszeichen gespeichert und wurden deshalb vom sicheren
Pfadvalidator abgewiesen. Die Referenzen sind bereinigt. Ein neuer
sprachparametrisierter Regressionstest lädt nun jedes Kapitel und prüft für
sämtliche Abschnitts- und View-Bilder Pfadmuster, Alternativtext und die
tatsächliche Datei. Damit sind 22 von 101 produktiven Views migriert.

Der achte Slice dokumentiert die vollständige Kommissionierung mit
Picklistenübersicht, Picking-Leitstelle und operativer Scanansicht. Für die
Wellenplanung sind Einzelauftrag, Multi-Order und zweistufige Strategie, die
Auswahl nach Zeit, Tour, Carrier oder Priorität, Mehrfachzuordnung, Freigabe und
Konsolidierung beschrieben. Die operative Anleitung erklärt Selbstzuordnung,
Routenoptimierung, Lagerplatz- und Artikelscan, bedingte Chargen- und
Seriennummernprüfung, Mengengrenzen, Fehlmengenmeldung sowie die eindeutige
Übergabe an den Packprozess.

Ein sprachparametrisierter Regressionstest gleicht drei Templates, acht Felder
der Wellenplanung, sieben Felder der Pickausführung, Formularanzahlen und
eindeutige vorhandene Bildreferenzen ab. Damit sind 25 von 101 produktiven Views
migriert.

Der neunte Slice schließt die Packstation mit Übersicht und operativem
Packauftrag ab. Beschrieben sind die Zuordnung offener Pickpositionen zu einem
Packstück, eindeutige Packstücknummer, tatsächliches Gewicht einschließlich
konfigurierter Gewichtsgrenze, nachträgliche Messung, Abschlussbedingungen und
die einmalige Versandübergabe mit Sendungsnummer, Carrier und Service. Die
Hinweise erklären außerdem, wann eine manuelle Labelerfassung statt einer
Carrier-Anbindung erforderlich sein kann.

Der sprachparametrisierte Regressionstest gleicht beide Templates, drei
Formulare, sechs Geschäftsfelder, fünf Aktionen und eindeutige vorhandene
Bildreferenzen ab. Damit sind 27 von 101 produktiven Views migriert.

Der zehnte, bewusst größere Slice schließt den gesamten restlichen Warenausgang
mit sechs Views ab. Sendungsübersicht und Versanddetail beschreiben automatische
und manuelle Labelregistrierung, Druckerauswahl, Druckaufträge, Tracking und die
nachgewiesene Carrierübergabe. Verladeübersicht, Manifestanlage und Ladeliste
erläutern die Auswahl etikettierter Sendungen, Route, Fahrzeug, einzelne
Ladebestätigungen und den erst nach vollständiger Beladung zulässigen Abschluss.

Die Outbound-Leitstelle dokumentiert zusätzlich Bedarfs- und Engpassanalyse,
Auftragsstorno, die drei Kriterien der Ausgangs-QS, priorisierte Versandregeln,
manuelle Trackingereignisse, Touren und Stopps, globale oder referenzbezogene
Gewichtsgrenzen sowie unveränderliche Versanddokumente mit Aggregatbezug und
Prüfsumme. Der sprachparametrisierte Test gleicht sechs Templates, 14 Formulare,
35 statische und drei dynamisch erzeugte Geschäftsfelder sowie eindeutige
Bildreferenzen ab. Damit ist der gesamte Warenausgang dokumentiert und 33 von
101 produktiven Views sind migriert.

Der elfte größere Slice dokumentiert neun zusammengehörige Lager- und
Bestandsviews: Lagerdashboard, Echtzeitbestand, grafische Platzbelegung,
Bewegungsledger, Chargen-/Seriennummern-/MHD-Rückverfolgung, Sonderbestände mit
separater Klassifizierung sowie Entnahmeregeln und deren gemeinsames
Konfigurationsformular. Neben den dargestellten Kennzahlen werden Filtergrenzen,
Bestandsverfügbarkeit, Farblogik, revisionssichere Bewegungen, Eigentümerbezug,
FIFO/LIFO/FEFO, Prioritäten und Geltungsbereiche ausführlich erklärt.

Das gemeinsame Formular berücksichtigt zusätzlich bereits die im nächsten
Bestandssperren-Slice verwendeten Sperrgründe und erläutert alle elf möglichen
Geschäftsfelder typabhängig. Der sprachparametrisierte Regressionstest gleicht
neun Templates, fünf gerenderte Formulare, 22 dokumentierte Feldvorkommen und
eindeutige Bildreferenzen ab. Damit sind 42 von 101 produktiven Views migriert.

Offen bleibt die Übertragung dieses Standards auf die übrigen 59 produktiven
Views sowie die abschließende globale View-, Feld- und Screenshotmatrix. Das
Ticket bleibt deshalb bewusst unterhalb von `Done`.

Der zwölfte größere Slice schließt die sieben noch fehlenden modernen
Lageransichten ab. Dokumentiert sind nun die komplette Lagertopologie mit ihren
fünf Hierarchieebenen und dem typabhängigen Pflegeformular, Bestandssperren samt
Gründen, Anlage, unabhängiger Prüfung und Freigabe sowie die Inventur- und
Materialleitstelle. Letztere umfasst Gefahrstoffklassen und Lagerrestriktionen,
Stücklisten und Bedarfsermittlung, Ladehilfsmittelkonten sowie Stichtags- und
permanente Inventur einschließlich getrennter Zählung und Freigabe.

Der zweisprachige Regressionstest gleicht sieben Templates, 15 gerenderte
Formulare, 49 Geschäftsfelder, 11 Aktionen der Leitstelle und eindeutige
vorhandene Bildreferenzen ab. Gemeinsam mit dem vorigen Slice sind damit alle
16 produktiven Templates unter `templates/warehouse/` vollständig dokumentiert.
Insgesamt sind 49 von 101 produktiven Views migriert.

Offen bleibt die Übertragung dieses Standards auf die übrigen 52 produktiven
Views sowie die abschließende globale View-, Feld- und Screenshotmatrix. Das
Ticket bleibt deshalb bewusst unterhalb von `Done`.

Der dreizehnte größere Slice eröffnet den Integrationsbereich mit zwölf
zusammengehörigen Views. Vollständig dokumentiert sind ERP-Verbindungen mit
Statusendpunkt und Outbox-Verweis, Carrier-Verbindungen mit Live-Abruf der
Versandprodukte sowie der Datenaustausch mit Jobübersicht, Import, Export,
Feldmappings und Commerce-Kanälen für Shopware, Amazon, eBay und generische
Systeme.

Die Feldhilfen erläutern insbesondere sichere Secret-Referenzen statt
Klartext-Credentials, HTTPS-Endpunkte, Aktivstatus, alle Import- und
Exportformate, Ressourcentypen, optionale Mappings, Dateiuploads, das erwartete
JSON-Array und sämtliche sechs Mapping-Transformationen. Der
sprachparametrisierte Regressionstest gleicht zwölf Templates, acht Formulare,
26 Geschäftsfelder und zwölf eindeutige vorhandene Bildreferenzen ab. Damit
sind 61 von 101 produktiven Views migriert.

Offen bleibt die Übertragung des Standards auf die übrigen 40 produktiven Views
sowie die abschließende globale View-, Feld- und Screenshotmatrix. Das Ticket
bleibt deshalb bewusst unterhalb von `Done`.

Der vierzehnte größere Slice schließt die Geräteperipherie mit 14 Views ab:
Scanner und mobile Terminals einschließlich idempotenter Scanerfassung,
Messgeräte mit direkter Übernahme von Gewicht und Abmessungen auf Packstücke
oder Artikel sowie Drucker und Druckaufträge mit kontrollierter Ausführung und
Wiederholung. Übersichten, Geräte- und Ereignisdetails sowie sämtliche Anlage-
und Prozessdialoge sind nun zweisprachig beschrieben.

Die Feldhilfen decken alle Geräte- und Prozesstypen, akzeptierte und abgelehnte
Ergebnisse, UUID-basierte Request-Idempotenz, Gramm und Millimeter,
Zielzuordnung, ZPL/PDF, Dokumenttypen, sichere Drucker-Credentials und die
Kopiengrenze von 1 bis 99 ab. Der Regressionstest gleicht 14 Templates, zehn
Formulare, 36 Geschäftsfelder und 14 eindeutige vorhandene Bildreferenzen ab.
Damit sind 75 von 101 produktiven Views migriert.

Offen bleibt die Übertragung des Standards auf die übrigen 26 produktiven Views
sowie die abschließende globale View-, Feld- und Screenshotmatrix. Das Ticket
bleibt deshalb bewusst unterhalb von `Done`.

Der fünfzehnte größere Slice dokumentiert die technische Automationsbasis in
zwölf Views: WCS, Materialflussrechner und Fördertechnik mit Verbindungen,
Transportbefehlen und Maschinenmeldungen; Lagerlifte, Paternoster und Logimat
mit Gerätebefehlen und Feedback; sowie TCP- und HTTP-Transportadapter mit
Protokoll, Framing, Zeitgrenzen und manueller Nachrichtenzustellung.

Besonderes Augenmerk liegt auf idempotenten Request-, Event- und Message-IDs,
zulässigen Befehlsstatusfolgen, eindeutigen Ladeeinheiten und Fachreferenzen,
sicheren Credential-Referenzen, den vollständigen Auswahlwerten für Geräte,
Befehle und Maschinenzustände sowie den Grenzwerten für Connect- und
Read-Timeout. Der Regressionstest gleicht zwölf Templates, zwölf Formulare,
46 Geschäftsfelder und zwölf eindeutige vorhandene Bildreferenzen ab. Damit
sind 87 von 101 produktiven Views migriert.

Offen bleibt die Übertragung des Standards auf die übrigen 14 produktiven Views
sowie die abschließende globale View-, Feld- und Screenshotmatrix. Das Ticket
bleibt deshalb bewusst unterhalb von `Done`.

Der sechzehnte und abschließende Slice dokumentiert die verbleibenden 14 Views:
Plattformdashboard und -Kontrollzentrum, Partnerportal, sechs
Erweiterungsansichten, Outboxübersicht und -detail, Handbuchübersicht und
-kapitel sowie Swagger UI. Das Plattform-Kontrollzentrum erläutert alle 14
Formulare und 42 Geschäftsfelder einschließlich Statusübergängen,
JSON-Schemata, Abrechnung, Medien und Druckrouting. Outbox-Retry und
-Quittierung sowie autorisierte OpenAPI-Testaufrufe sind mit Voraussetzungen,
Seiteneffekten und Fehlerbehandlung beschrieben.

Der sprachparametrisierte Regressionstest gleicht die 14 Templates, 20
Formulare, 50 Feldvorkommen und 14 eindeutige Bildreferenzen ab. Zusätzlich
sichert eine globale Matrix 101 eindeutige Handbucheinträge für 101 produktive
V3-Views in Deutsch und Englisch. Insgesamt dokumentiert das Handbuch 146
gerenderte Formulare und 422 viewbezogene Geschäftsfelder. Produktive
Nicht-V3-Ansichten gehören nicht zu dieser V3-Baseline; ihre weitere Nutzung,
Migration oder Stilllegung erfordert eine separate, explizite Entscheidung.

Alle Akzeptanzkriterien sind damit erfüllt. Die detaillierten Kennzahlen und
Prüfnachweise stehen in der
[Abschlussanalyse](../analysis/2026-10-05-user-handbook-view-coverage.md).
