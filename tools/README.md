# `tools/`: Hilfsskripte und Werkzeuge

Entwicklungs- und Betriebs-Hilfsmittel, die nicht Teil der Website sind.

## Inhalt

- `scripts/`: Wrapper für häufige Aufgaben (u.a. `unit_tests.sh`,
  `integration_tests.sh`, `system_tests.sh`, `test.sh`, `console.sh`,
  `check_migrations.sh`, `jest_tests.sh`). Viele davon sind als
  `composer`-Skripte verfügbar (siehe `composer.json`).
- `decrypt_backup/`: Entschlüsseln von Datenbank-Backups.
- `notify/`: `telegram_webhook_simulator.php` zum lokalen Testen des
  Telegram-Webhooks.
- `fuer_einsteiger/`: Bild-/Asset-Aufbereitung für die Seite "Für Einsteiger"
  (`improve.sh`, `copy-to-dev-server.sh`).
