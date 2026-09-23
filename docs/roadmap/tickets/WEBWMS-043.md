---
id: WEBWMS-043
issue_type: Story
epic: WEBWMS-EPIC-FULFILLMENT
status: Done
priority: High
story_points: 8
component: "Transport & Kommissionierung"
feature_group: "Transport"
source_feature: CG-043
---

# WEBWMS-043: Transportregeln implementieren

## User Story

Als Kommissionierer oder Disponent möchte ich die Funktion „Transportregeln“ nutzen, damit quell-, Ziel-, Prioritäts- und Ressourcenzuordnung konfigurieren.

## Fachlicher Umfang

Quell-, Ziel-, Prioritäts- und Ressourcenzuordnung konfigurieren.

**Prozesskontext:** Ereignis → Regel → Transport

## Akzeptanzkriterien

1. Berechtigte Benutzer können „Transportregeln“ im vorgesehenen Prozess ausführen.
2. Das System erfüllt folgendes fachliches Ergebnis: Quell-, Ziel-, Prioritäts- und Ressourcenzuordnung konfigurieren.
3. Der Ablauf „Ereignis → Regel → Transport“ ist vollständig abbildbar; ungültige Zustandswechsel werden verhindert.
4. Relevante Änderungen werden mit Zeitpunkt und ausführendem Benutzer nachvollziehbar gespeichert.
5. Erfolgs-, Fehler- und Grenzfälle sind durch automatisierte Tests abgedeckt.

## Technische Hinweise

Vorgesehene Entities: TransportRule, TransportOrder.
Vorgesehener Service: TransportRuleEngine.
Die Fachlogik liegt in einem Anwendungs-/Domänenservice; Controller und UI bleiben frei von Geschäftslogik.
Schreiboperationen erfolgen transaktional. Externe Nebenwirkungen werden bei Bedarf über Domain Events und Outbox verarbeitet.
Doctrine-Migration, Fixtures/Testdaten, Validierung, Autorisierung und PHPStan-konforme Typisierung sind Bestandteil der Umsetzung.

## Abhängigkeiten

Abhängig von den Stammdaten und Basiskomponenten des Epics WEBWMS-EPIC-FULFILLMENT. Konkrete technische Abhängigkeiten werden im Refinement anhand des bestehenden Codes ergänzt.

## Implementierungsstand

**Status:** Done

Priorisierte Transportregeln verknüpfen Auslöser, Quell- und Zielpräfixe, Transportart und Ressourcentyp. Passende Regeln erzeugen mandantengetrennt einen Fahrbefehl über API v3.

## Quelle

- Feature: CG-043
- Referenz: https://www.coglas.com/funktionen/
