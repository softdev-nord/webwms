# V3-Übersichten und CRUD-Konventionen

Die V3-Oberfläche trennt ab sofort konsequent zwischen Übersichten, Erfassung, Bearbeitung und operativen Prozessaktionen.

## Verbindliches Muster

- `GET /...` zeigt ausschließlich Liste, Filter, Suche, Paginierung und Navigation.
- `GET /.../new` zeigt ausschließlich die Erfassung eines neuen Eintrags.
- `POST /...` legt den Eintrag an.
- `GET /.../{id}/edit` zeigt einen vorhandenen Eintrag in einer eigenen Bearbeitungsseite.
- `POST /.../{id}` aktualisiert den Eintrag mandantensicher und auditierbar.
- Tabellen besitzen eine letzte, nicht sortier- und nicht durchsuchbare Spalte **Aktionen**.
- Operative Zustandswechsel bleiben auf einer separaten Detail-/Bearbeitungsseite und werden nicht als umfangreiche Inline-Formulare in der Übersicht dargestellt.

Die bestehende DataTables-Konvention bleibt erhalten: Seitengrößen 10/25/50/100, Volltextsuche, kombinierbare Filter, Sortierung, responsive deutsche Oberfläche und 30 Tage gespeicherter Zustand.

## Überarbeitete Bereiche

- Lagertopologie: getrennte Listen für Standorte, Lager, Bereiche, Gänge und Lagerplätze; separate Anlage- und Bearbeitungsseiten
- Entnahmestrategien: reine Regelübersicht sowie separate Anlage-/Bearbeitung
- Sonderbestände: Kennzeichenübersicht, separate Anlage-/Bearbeitung und separate Klassifizierungsseite je Bestand
- Bestandssperren: separate Sperrgründe, separate Sperrerfassung und separate Bearbeitungs-/Journalansicht

Änderungen an Konfigurationen werden im vorhandenen `wms_administration_event` mit Vorher-/Nachher-Payload, Benutzer und Zeitpunkt protokolliert.

