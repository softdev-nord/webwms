# Benutzerdokumentation im V3-Frontend

Die Markdown-Dateien unter `docs/user` sind die einzige Quelle der Benutzerdokumentation. Das V3-Frontend liest sie zur Laufzeit ein und stellt sie authentifizierten Benutzern unter `/v3/help` bereit. Dadurch ist keine zweite, manuell zu synchronisierende HTML-Dokumentation erforderlich.

## Komponenten

- `UserDocumentationService` entdeckt Markdown-Dateien, liest Titel und Kurzbeschreibung aus, gruppiert sie fachlich und stellt Suche sowie Seitennavigation bereit.
- `SafeMarkdownRenderer` konvertiert den benötigten Markdown-Umfang in HTML. Unterstützt werden Überschriften, Absätze, geordnete und ungeordnete Listen, Tabellen, Codeblöcke, Inline-Code, Hervorhebungen und Links.
- `V3DocumentationController` liefert Übersicht und Detailseiten.
- Die Templates unter `templates/v3/documentation` verwenden Karten, Typografie, Farben und responsive Raster der bestehenden V3-Oberfläche.

## Sicherheit

Dokument-Slugs werden auf Kleinbuchstaben, Zahlen und Bindestriche beschränkt. Dateien außerhalb von `docs/user` können nicht angefordert werden. Markdown-Inhalte werden HTML-escaped; eingebettetes Roh-HTML wird nicht ausgeführt. Links erlauben nur HTTP(S), E-Mail, Seitenanker und Referenzen auf andere Markdown-Dateien in `docs/user`. Relative Markdown-Verweise werden automatisch in klickbare `/v3/help/{slug}`-Links umgeschrieben.

## Neue Seiten ergänzen

Eine neue Datei `docs/user/thema.md` mit einer H1-Überschrift wird automatisch in die Übersicht aufgenommen. Die fachliche Gruppierung erfolgt zentral im `UserDocumentationService`. Änderungen an bestehenden Markdown-Dateien sind ohne Anpassung eines HTML-Templates sichtbar.

Die Dokumentation benötigt keine fachliche Mandantenberechtigung, ist aber durch die bestehende V3-Firewall ausschließlich für vollständig angemeldete Benutzer erreichbar.
