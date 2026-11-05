# `Command/`: Console-Commands

Symfony Console-Commands für Aufgaben ausserhalb des normalen Anfrage/Antwort-Flusses
– typischerweise, weil sie lange dauern, regelmässig (z.B. täglich) laufen oder
einmalige Wartungs-/Administrationsaufgaben sind (Daten-Sync, Benachrichtigungen,
Backups, Aufräumjobs, …).

- Commands liegen in `src/Command/`, erweitern `OlzCommand`
  ([`Common/OlzCommand.php`](./Common/OlzCommand.php)) und werden mit
  `#[AsCommand(name: 'olz:...')]` registriert.
- `getAllowedAppEnvs()` legt fest, in welchen App-Umgebungen ein Command laufen darf.
- Ausführen: `php bin/console olz:...`.
- Wiederkehrende Aufgaben (CRON-Job) bündelt `olz:on-continuously`.
- Commands können ausserdem über die "Commands"-App bzw. das `executeCommand`-API ausgelöst werden.

Symfony-Doku:
- Commands: https://symfony.com/doc/current/console.html
- Console-Component: https://symfony.com/doc/current/components/console.html

## Tests

- Zu jedem Command gehört ein Unit-Test unter `tests/UnitTests/Command/`
  (erzwungen durch `tests/UnitTests/AllCommandsCoveredTest.php`).
- Für Commands, die mit der Datenbank arbeiten, ist zusätzlich ein
  Integration-Test unter `tests/IntegrationTests/Command/` sinnvoll.
- Details: [`tests/README.md`](../../tests/README.md).
