# `tests/`: Tests

Hier liegen alle automatisierten Tests. Es gibt drei Arten von Tests:

| Art | Ordner | Braucht Datenbank | Basisklasse |
| --- | --- | --- | --- |
| Unit Tests | `tests/UnitTests/` | nein (Fakes) | `UnitTestCase` |
| Integration Tests | `tests/IntegrationTests/` | ja (Test-Datenbank) | `IntegrationTestCase` |
| System Tests | `tests/SystemTests/` | ja (Browser + Server) | `SystemTestCase` |

## Wo gehört mein Test hin?

Der Ordner-Aufbau unter `tests/UnitTests/` und `tests/IntegrationTests/` spiegelt
grob den Aufbau von `src/`. Ein Test für `src/Command/FooCommand.php` liegt also
zum Beispiel unter `tests/UnitTests/Command/FooCommandTest.php` bzw.
`tests/IntegrationTests/Command/FooCommandIntegrationTest.php`.

## Fakes

`tests/Fake/` enthält "Fakes" für alles, was sonst von aussen abhängt (Datenbank,
Netzwerk, Services). Unit-Tests benutzen diese Fakes statt echter Infrastruktur und
laufen dadurch schnell und deterministisch. Auch Integration-Tests setzen Fakes
gezielt ein, um externe Dienste (z.B. Netzwerk-Aufrufe) zu ersetzen. Details:
[`Fake/README.md`](./Fake/README.md).

## Fixtures

Statische Test- und Dev-Daten (Beispiel-Dateien und Datenbank-Dumps) liegen unter
[`fixtures/`](../fixtures/README.md) - bewusst ausserhalb von `src/`, da sie nicht
Teil des Anwendungscodes sind.

## Konventionen

- Jede Test-Klasse beginnt mit `declare(strict_types=1);`.
- Doc-Block mit `@internal` und `@covers \Olz\...` (bzw. `@coversNothing` für
  reine Test-Hilfsklassen).

## Weiter

- [`UnitTests/README.md`](./UnitTests/README.md)
- [`IntegrationTests/README.md`](./IntegrationTests/README.md)
- [`SystemTests/README.md`](./SystemTests/README.md)
