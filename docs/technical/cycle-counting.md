# Permanente und Nulldurchgangsinventur

Dieser Slice erweitert den Inventur-Core um `WEBWMS-030` und `WEBWMS-031`.

## Permanente Inventur

`CycleCountPlan` beschreibt einen aktiven Zählplan durch Mandant, Lager, Lagerplatzpräfix, Intervall in Tagen und nächste Fälligkeit. `CreateCycleCountPlanHandler` persistiert den Plan. `StartDueCycleCountHandler` startet ausschließlich fällige Pläne und erzeugt einen Inventurbeleg vom Typ `permanent` mit einer Momentaufnahme aller Bestände im definierten Bereich.

Die Erzeugung erfolgt transaktional. Ein bereits offener oder gezählter Beleg für denselben Lagerbereich verhindert eine parallele Zählung. Nach erfolgreicher Erzeugung werden `last_started_at` und `next_due_at` atomar fortgeschrieben.

## Nulldurchgangsinventur

`DbalInventoryRepository` prüft Bestandsabgänge aus regulären Buchungen, Umlagerungen und Allokationsverbräuchen. Wechselt ein gesperrter Bestand von einer positiven Menge exakt auf null, wird nach der Ledger-Buchung in derselben Transaktion ein Inventurbeleg vom Typ `zero_crossing` erzeugt.

Der Beleg enthält genau die betroffene Kombination aus Artikel, Lagerplatz und Bestandsdimensionen. `trigger_ledger_entry_id` stellt den Auditbezug zur auslösenden Buchung her. Ein Unique-Index und eine Prüfung auf offene Kontrollen verhindern Duplikate.

## Datenbank

Migration `Version20260918160000` ergänzt:

- `wms_cycle_count_plan` für Planung, Intervalle und Fälligkeiten;
- `count_type`, `cycle_count_plan_id` und `trigger_ledger_entry_id` an `wms_inventory_count`;
- Indizes und Fremdschlüssel für Fälligkeit, Belegtyp und auslösende Ledger-Buchung.

## Gemeinsamer Folgeprozess

Beide Inventurarten verwenden anschließend den bestehenden Ablauf:

1. Zählmenge erfassen;
2. Inventur einreichen;
3. Differenzen durch eine zweite Person freigeben;
4. Bestandskorrekturen im Ledger buchen.

## Bekannte Restarbeiten

- API-Ressourcen und Benutzeroberfläche;
- explizite Berechtigungen pro Inventuraktion;
- Scheduler/CLI zur automatischen Auswahl aller fälligen Pläne;
- Datenbank-Integrationstests gegen MariaDB.
