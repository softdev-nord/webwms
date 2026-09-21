# Geplanter Wareneingang in V3

Der V3-Arbeitsplatz verbindet den vorhandenen Backendkern für avisierte Wareneingänge, digitale QS und automatische Einlagerung zu einem durchgängigen Ablauf.

## Ablauf

1. `ReceiveInboundDeliveryHandler` übernimmt eine avisierte Position und erzeugt einen Eingang im Status `pending_quality`.
2. `InspectInboundReceiptHandler` verarbeitet die QS-Checkliste, bucht den Bestand auf den Annahmeplatz und setzt abhängig von der Entscheidung den Status `available` oder `blocked`.
3. `CreatePutawayOrderHandler` wählt mandantenbezogen anhand der aktiven Einlagerungsstrategie einen geeigneten Zielplatz.
4. `ConfirmPutawayHandler` führt die Bestandsumlagerung atomar aus und schließt den Einlagerungsauftrag ab.

Die Worklist aus `ApiV3QueryService::plannedInboundWorklist()` beschränkt alle Ergebnisse über `wms_inbound_delivery.tenant_id`. Schreibzugriffe verwenden die bestehenden transaktionalen Repository-Operationen und speichern Benutzer sowie Zeitpunkte.

## Endpunkte und Berechtigungen

| Aktion | Web/API | Berechtigung |
| --- | --- | --- |
| Worklist lesen | `/v3/inbound/planned`, `GET /api/v3/inbound/planned` | `inbound.planned.read` |
| Avisierte Position annehmen | POST auf `.../receive` | `inbound.planned.receive` |
| QS abschließen | POST auf `.../inspect` | `inbound.planned.inspect` |
| Einlagerung planen/bestätigen | POST auf `.../putaway` | `inbound.planned.putaway` |

Der Demo-Bootstrap legt Bestellung, Avis, Position und eine Einlagerungsstrategie an. Dadurch kann der Prozess nach erneutem Bootstrap direkt in der Oberfläche getestet werden.
