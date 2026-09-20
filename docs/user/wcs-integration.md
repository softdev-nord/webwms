# WCS, MFR und Fördertechnik verwenden

Unter **Integration & Technik → WCS, MFR und Fördertechnik** verwalten berechtigte Benutzer Verbindungen, Transportbefehle und Maschinenrückmeldungen.

## Verbindung und Transportbefehl

1. Über **Verbindung anlegen** ein WCS, einen Materialflussrechner oder eine Fördertechnik mit HTTPS-Endpoint konfigurieren.
2. Als Credential nur den Namen der bereitgestellten Umgebungsvariable eintragen.
3. Über **Befehl anlegen** Quelle, Ziel und Ladeeinheit erfassen.
4. Den Befehl nacheinander als übermittelt, angenommen und abgeschlossen zurückmelden. Alternativ kann er fehlschlagen oder abgebrochen werden.

Pausierte Verbindungen nehmen keine neuen Befehle oder Statusmeldungen an. Terminale Befehle lassen keine weiteren Zustandswechsel zu.

## Maschinenstatus

Über **Status erfassen** kann ein Zustand wie bereit, beschäftigt, blockiert, Störung oder offline protokolliert werden. Der optionale Befehlsbezug ordnet die Rückmeldung einem Transport zu. Die externe Ereignis-ID verhindert doppelte Einträge bei wiederholter Übermittlung.
