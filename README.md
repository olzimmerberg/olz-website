# Website der OL Zimmerberg

## Code laden

- Installiere `git`
- [Klone dieses Repository](https://help.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)

## Development-Server konfigurieren

- Installiere `PHP`
- Installiere `MySQL`
- Erstelle eine MySQL Datenbank
- Importiere einen Export der Online-Datenbank
- Öffne ein Terminal im Repository-Klon-Ordner
- Kopiere `dev-server/config.template.php` nach `dev-server/config.php`
- Gib die Zugangsdaten zur lokalen Datenbank in `dev-server/config.php` ein
- Achte darauf, dass `dev-server/config.php` keinen Zeilenumbruch ausserhalb des `<?php ... ?>` Tags enthält

## Development-Server starten

- Öffne ein Terminal im Repository-Klon-Ordner
- Starte den Development-Server: `./run.sh`

## Code beitragen

[Mit git](https://git-scm.com/book/en/v2)

## Automatische Veröffentlichung

- Wenn ein neuer Branch gepusht wird, werden diese Änderungen automatisch auf [test.olzimmerberg.ch](https://test.olzimmerberg.ch) veröffentlicht.
- Wenn ein Pull-Request gemerged wird, werden diese Änderungen automatisch auf [olzimmerberg.ch](https://olzimmerberg.ch) veröffentlicht.

Der Fortschritt dieser automatischen Veröffentlichungen kann [auf GitHub](https://github.com/olzimmerberg/olz-website/actions) verfolgt werden.
