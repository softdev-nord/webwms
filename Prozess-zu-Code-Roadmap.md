Ich erstelle dir jetzt die konkrete *Prozess-zu-Code-Roadmap* mit EPICs, Tickets und Akzeptanzkriterien, startend mit Wareneingang Ende-zu-Ende.
- [x] **EPIC-Struktur für fehlende WMS-Kernprozesse**
- [x] **Ticketliste mit technischer Zuordnung im bestehenden Code**
- [x] **Akzeptanzkriterien pro Ticket**
- [x] **Vorschlag für Umsetzungsreihenfolge in Iterationen**
*High-Level-Plan:* Zürst den operativen Kern (Wareneingang -> Einlagerung -> Bestandsupdate -> Historie) fertigstellen, danach Prozesssicherheit (Workflow, Inventur, Fehlerpfade), dann Optimierung (Performance, Security, Monitoring).

### EPIC 1: Wareneingang Ende-zu-Ende (Priorität: P1)
Ziel: Durchgängiger Prozess von WE-Meldung bis transportierbarer Einlagerungsaufgabe.
#### T1.1 Prozesslogik SI102/SI103/SI106 implementieren
Code-Fokus: src/Event/Stock/In/StockInFromGoodsReceiptEvent.php, src/Event/Stock/In/StockInFromProductionEvent.php, src/Event/Stock/In/StockInForSupplierOrderEvent.php
Akzeptanz: Kein TODO, keine Example Response; valide Response mit Fachdaten.
#### T1.2 Formular- und Validierungsfluss verbinden
Code-Fokus: src/Form/Stock/*, src/Service/Validation/*, src/Controller/StockTransactionController.php
Akzeptanz: Fehlende Pflichtfelder liefern strukturierte Fehler, kein stilles null.
#### T1.3 Transportauftragserzeugung robust machen
Code-Fokus: src/Service/TransportRequest/TransportRequestService.php, src/Service/DataHandlers/TransportRequest/TransportRequestDataHandler.php
Akzeptanz: TA-Erzeugung transaktional, rollback bei Fehlern, eindeutige Statusantwort.
#### T1.4 Bestands- und Historienbuchung synchronisieren
Code-Fokus: src/Service/Stock/StockOccupancyService.php, src/Service/TransportHistory/TransportHistoryService.php
Akzeptanz: WE bucht incoming/inStock konsistent; Historie nachvollziehbar.

### EPIC 2: Auslagerung/Umlagerung/Lending produktiv (P1)
Ziel: Alle Menüpunkte mit echter Logik statt Platzhaltern.
#### T2.1 SO-Prozesse implementieren
Code-Fokus: src/Event/Stock/Out/*
Akzeptanz: Direktauslagerung, auf Kostenstelle, aus Container/Zone funktionieren fachlich.
#### T2.2 ST-Prozesse implementieren
Code-Fokus: src/Event/Stock/Transfer/*
Akzeptanz: Umlagerungen erzeugen korrekte Quell-/Zielbewegungen.
#### T2.3 Lending-Prozesse implementieren
Code-Fokus: src/Event/Stock/Lending/*
Akzeptanz: Ausleihe/Rückgabe erzeugen prüfbare Bestandsbewegungen.

### EPIC 3: Workflow & Statusmaschine für Transportaufträge (P1)
Ziel: Steuerbarer Lifecycle statt impliziter Zustandswechsel.
#### T3.1 Symfony Workflow aktivieren
Code-Fokus: config/packages/workflow.yaml, src/Entity/TransportRequest.php
Akzeptanz: Definierte States/Transitions (open, in_progress, done, cancelled).
#### T3.2 Command-Endpoints für Transitions
Code-Fokus: src/Controller/TransportRequestController.php
Akzeptanz: Start/Abschluss/Storno als eigene Aktionen mit Guard-Regeln.
#### T3.3 Delete-Pfad fachlich ersetzen
Code-Fokus: src/Controller/TransportRequestController.php:deleteTransportRequest
Akzeptanz: Kein return null; stattdessen cancel/archive mit sauberer Antwort.

### EPIC 4: Inventur vollständig (P1/P2)
Ziel: Inventurprozess operativ nutzbar.
#### T4.1 Inventurstart mit Snapshot
Code-Fokus: module/StockInventoryBundle/*
Akzeptanz: Inventurauftrag mit Zeitpunkt, Bereich, Status.
#### T4.2 Zählung + Differenz
Code-Fokus: neue Inventur-Entitäten + Controller/Service
Akzeptanz: Ist/Soll-Differenzen automatisch berechnet.
#### T4.3 Inventurbuchung + Abschluss
Code-Fokus: Stock/History-Services
Akzeptanz: Differenzen buchen, auditierbarer Abschlussbericht.

### EPIC 5: Prozessqualität & Robustheit (P2)
Ziel: Weniger Produktionsfehler, bessere Wartbarkeit.
#### T5.1 return null in Controllern entfernen
Code-Fokus: src/Controller/*.php
Akzeptanz: 404/422 mit klarer Nutzermeldung statt Leerlauf.
#### T5.2 Response-Trennung scharfziehen
Code-Fokus: src/Service/DataHandlers/*
Akzeptanz: DataHandler liefern Datenobjekte/Arrays, Controller baut JsonResponse.
#### T5.3 Transaktionsgrenzen einziehen
Code-Fokus: schreibende DataHandler/Services
Akzeptanz: Mehrschritt-Buchungen atomar.

### EPIC 6: Performance, Sicherheit, Betrieb (P2)
Ziel: Skalierbarkeit und Betriebssicherheit.
#### T6.1 N+1 in Artikelübersicht beheben
Code-Fokus: src/Modules/Article/Infrastructure/Query/DoctrineArticleOverviewReadModelRepository.php
Akzeptanz: Aggregation in SQL/Projection, konstante Query-Anzahl pro Seite.
#### T6.2 Access-Control präzisieren
Code-Fokus: config/packages/security.yaml
Akzeptanz: Rollen je Prozessroute sauber hinterlegt.
#### T6.3 Monitoring-Events/Logs standardisieren
Code-Fokus: src/Service/LoggingService.php, Prozesscontroller
Akzeptanz: Einheitliche Prozess-Log-IDs, Fehlerkorrelation möglich.

Umsetzungsreihenfolge (4 Iterationen)
- Iteration 1: EPIC 1 + T5.1
- Iteration 2: EPIC 2 + EPIC 3
- Iteration 3: EPIC 4
- Iteration 4: EPIC 5 (rest) + EPIC 6

Definition of Done pro Ticket
Fachlicher Happy Path + 2 Fehlerpfade getestet
Kein TODO/Example Response im betroffenen Prozess
Keine stillen null-Rückgaben in Controller-Actions
Prozesslog mit Benutzer, Zeit, Aktion, Ergebnis
Mindestens 1 Integrationstest für den Endpunkt