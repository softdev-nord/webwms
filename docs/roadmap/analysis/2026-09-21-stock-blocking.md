# Abschlussanalyse Bestandssperren vom 21.09.2026

WEBWMS-025 schließt die Lücke zwischen dem bereits vorhandenen Bestandsstatus `blocked` und einem operativ kontrollierbaren Sperrprozess.

| Bereich | Ergebnis |
| --- | --- |
| Konfiguration | Mandantenspezifische, aktive Sperrgründe |
| Sperrung | Dimensionsgenaue Statusumbuchung unter Berücksichtigung aktiver Allokationen |
| Prüfung | Verpflichtender eigener Zustand mit Prüfer und Notiz |
| Freigabe | Nur nach Prüfung, Rückbuchung in den ursprünglichen Status |
| Nachweis | Unveränderliches Ereignisjournal, UI, API, Rechte und Tests |

WEBWMS-025 erfüllt damit seine Akzeptanzkriterien und wechselt auf `Done`. Als nächster Inventory-Slice folgt fachlich WEBWMS-026 Gefahrstoffverwaltung; alternativ können die bereits vorhandenen Inventur-Backends WEBWMS-029 bis WEBWMS-032 vertikal im Frontend abgeschlossen werden.
