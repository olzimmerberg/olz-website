# Website der OL Zimmerberg

## Get started

### Code laden

- Installiere `git`
- [Klone dieses Repository](https://docs.github.com/en/repositories/creating-and-managing-repositories/cloning-a-repository)

### Development-Server konfigurieren

- Installiere `PHP`
- Installiere `composer`
- Installiere `MySQL`
- Erstelle eine MySQL Datenbank
- Kopiere `public/config.template.php` nach `public/config.php`
- Gib die Zugangsdaten zur lokalen Datenbank in `public/config.php` ein
- Achte darauf, dass `public/config.php` keinen Zeilenumbruch ausserhalb des `<?php ... ?>` Tags enthält

### Development-Server starten

- Öffne ein Terminal im Repository-Klon-Ordner
- Starte den Development-Server: `composer run`
- Betrachte das Resultat auf [`http://127.0.0.1:30270/`](http://127.0.0.1:30270/)

### Development-Daten laden

- Starte den Development-Server (siehe oben)
- Gehe in einem Browser zur URL [`http://127.0.0.1:30270/tools.php/reset`](http://127.0.0.1:30270/tools.php/reset)
- Betrachte das Resultat auf [`http://127.0.0.1:30270/`](http://127.0.0.1:30270/)

### Tests laufen lassen

- Unit Tests: `composer unit_tests`
- Integration Tests: `composer integration_tests`
- Für den Code-Coverage-Report: `composer test`

### Code beitragen

[Mit git](https://git-scm.com/book/en/v2)

## Aufbau / Entwicklung

### Apps

siehe [`src/Apps`](./src/Apps/).

### Symfony-Migration

siehe [`_`](./_/).

### OLZ-API

siehe [`src/Api`](./src/Api/).

### Komponenten

siehe [`src/Components`](./src/Components/).

### Namen / Sprache

Wo Umlaute zu Problemen führen könnten, sollen englische Namen verwendet werden.
Für den Benutzer sichtbarer Inhalt sollte immer deutsch sein.
Bei einem Konflikt der beiden vorherigen Regeln sollten deutsche Namen mit Zwei-Buchstaben-Umlauten verwendet werden.

Beispiele:
- Variablennamen: englisch
- Dateiname von Icons: englisch
- Was der Benutzer in der URL-Leiste des Browsers sehen kann: deutsch mit Zwei-Buchstaben-Umlauten
- Text eines Menu-Eintrags: deutsch

### Dateigrösse

Die Grösse des webpack-bundles kann wie folgt analysiert werden:

- `npm run webpack-build`
- `npm run webpack-analyze`

## Automatische Veröffentlichung

- Wenn ein neuer Branch gepusht wird, werden diese Änderungen automatisch auf [test.olzimmerberg.ch](https://test.olzimmerberg.ch) veröffentlicht.
- Wenn ein Pull-Request gemerged wird, werden diese Änderungen automatisch auf [olzimmerberg.ch](https://olzimmerberg.ch) veröffentlicht.

Der Fortschritt dieser automatischen Veröffentlichungen kann [auf GitHub](https://github.com/olzimmerberg/olz-website/actions) verfolgt werden.

### Manuelle Veröffentlichung

Falls die automatische Veröffentlichung *nicht* funktionieren sollte, z.B. weil GitHub Actions nicht funktionieren, kann auch manuell veröffentlicht werden:

- `PASSWORD=$PASSWORD php ./Deploy.php --environment=prod --username=deploy.olzimmerberg.ch`
- `$PASSWORD` = FTP Passwort für `deploy.olzimmerberg.ch`
