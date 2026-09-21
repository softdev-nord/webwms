# Mandant und Konfiguration verwalten

Öffnen Sie **Administration → Mandant & Konfiguration**. Dort verwalten Sie:

- Geschäftspartner und deren Datenräume,
- die Zuordnung vorhandener Standorte und Lager,
- OIDC- oder SAML-Identity-Provider ohne Ablage von Secrets in der Datenbank,
- Beleg- und Identnummernkreise einschließlich GS1-Unternehmenspräfix,
- aktivierbare Prozesse mit optionaler JSON-Konfiguration,
- Desktop-, Tablet- und Scannerprofile,
- das Betriebsprofil für SaaS, On-Premises oder Hybridbetrieb.

Unter **Administration → Benutzer & Rollen** können Benutzerrollen direkt geändert und bestehende Rollen bearbeitet werden. WebWMS verhindert, dass ein angemeldeter Administrator sich seine eigene Verwaltungsberechtigung entzieht oder sich selbst deaktiviert.

Jede relevante Konfigurationsänderung erscheint mit Zeitpunkt und ausführendem Benutzer im Änderungsjournal. Mit **Testnummer** kann kontrolliert eine Nummer aus einem Nummernkreis vergeben werden; dieser Vorgang erhöht den Zähler dauerhaft.
