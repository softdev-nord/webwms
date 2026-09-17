# Mandanten und Standorte

## Mandant

Ein Mandant bildet ein Unternehmen oder eine organisatorisch getrennte Einheit
ab. Beim Anlegen werden Name und eine UUID gespeichert. Ein deaktivierter
Mandant bleibt für Historie und Auswertungen erhalten, kann aber nicht geändert
werden.

## Standort

Ein Standort bildet einen physischen Lager- oder Betriebsstandort ab.
Erforderlich sind:

- zugehöriger Mandant;
- eindeutiger Standortcode, beispielsweise `BER-01`;
- Anzeigename;
- gültige Zeitzone, beispielsweise `Europe/Berlin`.

Standortcodes werden automatisch in Großbuchstaben umgewandelt. Ein Code darf
innerhalb desselben Mandanten nur einmal vorkommen.

Ein Standort kann deaktiviert und später wieder aktiviert werden. Während er
inaktiv ist, können seine Stammdaten nicht geändert werden.
