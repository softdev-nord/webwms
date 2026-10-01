# WCS, MFR und Fördertechnik verwenden

Unter **Integration & Technik → WCS, MFR und Fördertechnik** verwalten berechtigte Benutzer Verbindungen, Transportbefehle und Maschinenrückmeldungen.

## Verbindung und Transportbefehl

1. Über **Verbindung anlegen** ein WCS, einen Materialflussrechner oder eine Fördertechnik mit HTTPS-Endpoint konfigurieren.
2. Als Credential nur den Namen der bereitgestellten Umgebungsvariable eintragen.
3. Über **Befehl anlegen** Quelle, Ziel und Ladeeinheit erfassen.
4. WebWMS stellt den Befehl automatisch über die Integrations-Outbox zu und markiert ihn nach erfolgreicher Übergabe als übermittelt.
5. Das Zielsystem meldet ihn anschließend als angenommen und abgeschlossen zurück. Alternativ kann er fehlschlagen oder abgebrochen werden.

Pausierte Verbindungen nehmen keine neuen Befehle oder Statusmeldungen an. Terminale Befehle lassen keine weiteren Zustandswechsel zu. Wiederholte Zustellungen verwenden dieselbe Nachrichten-ID, sodass WCS und MFR sie idempotent behandeln können. Dauerhaft fehlgeschlagene Zustellungen erscheinen in der Integrations-Outbox als Dead Letter und können nach Behebung erneut gestartet werden.

## Maschinenstatus

Über **Status erfassen** kann ein Zustand wie bereit, beschäftigt, blockiert, Störung oder offline protokolliert werden. Der optionale Befehlsbezug ordnet die Rückmeldung einem Transport zu. Die externe Ereignis-ID verhindert doppelte Einträge bei wiederholter Übermittlung.
