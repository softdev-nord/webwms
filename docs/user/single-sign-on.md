# Mit Single Sign-on anmelden

WebWMS unterstützt die Anmeldung am V3-Arbeitsbereich über einen OpenID-Connect-Identity-Provider.

## Identity Provider einrichten

1. Unter **Administration → Konfiguration → Identity Provider** einen eindeutigen Code und Namen vergeben.
2. Die HTTPS-Issuer-URL des Providers eintragen. WebWMS lädt daraus automatisch die Discovery-Metadaten.
3. Client-ID und den Namen der Umgebungsvariable für das Client-Secret erfassen. Das Secret selbst wird nicht in WebWMS gespeichert.
4. Mindestens die Scopes `openid profile email` konfigurieren und den Provider aktivieren.
5. Beim Identity Provider die in der WebWMS-Route erzeugte Callback-URL `/v3/sso/{Mandant}/{Provider}/callback` hinterlegen.

## Anmeldung

Auf der V3-Anmeldeseite unter **Mit Single Sign-on anmelden** die Mandanten-ID und den Provider-Code eingeben. Nach erfolgreicher Anmeldung beim Identity Provider kehrt der Browser zu WebWMS zurück und öffnet den V3-Arbeitsbereich.

Beim ersten Login wird die verifizierte E-Mail-Adresse einem bereits vorhandenen, aktiven WebWMS-Benutzer desselben Mandanten zugeordnet. WebWMS legt über SSO keine neuen Benutzer oder Rollen an. Ist kein passender Benutzer vorhanden, wird die Anmeldung abgelehnt.

Fehlgeschlagene Anmeldungen erscheinen im Login-Journal. Bei einem abgelaufenen Vorgang die Anmeldung erneut von der WebWMS-Anmeldeseite starten.
