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
