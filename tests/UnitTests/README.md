# `UnitTests/`: Unit Tests

Unit-Tests prüfen einzelne Klassen isoliert – ohne echte Datenbank, ohne Netzwerk
und ohne laufenden Server. Sie sind schnell und deterministisch.

## Aufbau

- Basisklasse: `UnitTestCase` (`tests/UnitTests/Common/UnitTestCase.php`).
- `UnitTestCase::setUp()` befüllt `WithUtilsCache` mit Fakes für alle Utils
  (z.B. `FakeEntityManager`, `FakeDbUtils`, `FakeDateUtils`, `FakeEnvUtils`, ...).
  Code, der `WithUtilsTrait` verwendet, bekommt dadurch automatisch die Fakes.
- Vor jedem Test wird `FakeEntity::reset()` aufgerufen, damit die Fakes "frisch"
  sind.
- `getLogs()` liest die Logs aus dem `FakeLogHandler`.

## Beispiel

```php
final class TestCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $command = new TestCommand();
        $this->assertSame(TestCommand::class, get_class($command));
    }
}
```

## Fakes anpassen

Über `WithUtilsCache::get('entityManager')` erhält man den `FakeEntityManager` und
kann z.B. über `->repositories[User::class]` das Verhalten der Repositories
steuern. Vordefinierte Test-Entities gibt es z.B. mit `FakeUser::defaultUser()`.
Mehr dazu: [`../Fake/README.md`](../Fake/README.md).
