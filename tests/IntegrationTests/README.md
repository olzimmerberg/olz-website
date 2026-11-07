# `IntegrationTests/`: Integration-Tests

Integration-Tests prüfen das Zusammenspiel mehrerer Komponenten mit einer **echten
Test-Datenbank**. Sie sind langsamer als Unit-Tests, decken dafür aber z.B.
Datenbank-Zugriffe und Commands realistisch ab.

## Voraussetzungen

- Kopiere `config/olz.test.template.php` nach `config/olz.test.php` und trage die
  Zugangsdaten einer **separaten** Test-Datenbank ein.
- Kopiere bei Bedarf `.env.test.local.template` nach `.env.test.local`.
- Die Test-Datenbank muss eine eigene Datenbank sein (Schema `..._test`). Die
  Integration-Tests setzen sie beim ersten Test zurück (`fullResetDb`): Tabellen
  werden gelöscht und mit den Daten aus `fixtures/db/content.sql` neu
  befüllt. **Nie auf die Dev- oder Prod-Datenbank zeigen lassen!**

Für die CI-Pipeline ist `DB_PORT` gesetzt; dort werden die Konfigurations-Dateien
(`config/olz.test.php`, `.env.test.local`) bei Bedarf aus den Templates erzeugt und
der Port angepasst. Für die lokale Entwicklung legst du diese Dateien einmalig
selbst aus den Templates an (siehe oben).

## Ausführen

- Ganze Suite: `composer integration_tests`
- Einzelner Test (schnell, ohne Coverage):
  `php bin/phpunit --colors=never --no-coverage tests/IntegrationTests/...`

## Aufbau

- Basisklasse: `IntegrationTestCase`
  (`tests/IntegrationTests/Common/IntegrationTestCase.php`). Sie bootet den
  Symfony-Kernel und verbindet sich mit der echten Test-Datenbank.
- Hilfsmethoden:
  - `withLockedDb(callable $fn)`: führt den Test exklusiv aus und setzt den
    DB-Inhalt danach zurück (`resetDbContent`).
  - `getLogs()` / `resetLogs()`: Logs des aktuellen Tests lesen bzw. leeren.
  - `getDevDataUtils()`: Zugriff auf `DevDataUtils` (z.B. `fullResetDb`,
    `resetDbContent`).
- Die SUT (und andere Services) holt man sich über
  `self::getContainer()->get(...)`.

## Test-Daten

Die Test-Datenbank wird aus `fixtures/db/content.sql` befüllt. Ändern sich
diese Daten, müssen ggf. auch Erwartungen in Integration-Tests angepasst werden.

## Fakes

Die Datenbank und der Symfony-Container sind echt; Fakes kommen in Integration-Tests
gezielt für externe Dienste zum Einsatz, die nicht Teil des Systems unter Test sind
(z.B. Netzwerk/Fetcher wie `FakeSolvFetcher`). So bleiben die Tests deterministisch
und müssen nicht ins Internet. Mehr dazu: [`../Fake/README.md`](../Fake/README.md).
