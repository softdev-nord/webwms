# Geplanten Wareneingang bearbeiten

Öffnen Sie **Wareneingang → Geplante Eingänge**. Die Liste zeigt jede avisierte Position und ihren aktuellen Prozessschritt.

1. Wählen Sie **Annehmen**, um eine angelieferte Position an die Qualitätsprüfung zu übergeben.
2. Wählen Sie einen Annahmeplatz, beantworten Sie die Prüffragen und entscheiden Sie zwischen **Freigeben** und **Sperren**. Fehlgeschlagene Prüfpunkte können nicht freigegeben werden.
3. Mit **Einlagerung planen** ermittelt WebWMS anhand der Strategie automatisch einen Zielplatz.
4. Prüfen Sie den angezeigten Zielplatz und wählen Sie **Einlagerung bestätigen**. WebWMS bucht den Bestand atomar vom Annahme- auf den Lagerplatz um.

Der Demo-Datensatz `DEMO-IN-001` steht nach `webwms:v3:demo-bootstrap` als avisierter Eingang bereit.
