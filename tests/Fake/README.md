# `Fake/`: Fakes für Tests

Fakes ersetzen in Tests alles, was sonst von aussen abhängt: Datenbank,
Dateisystem, Netzwerk, Mailer, Telegram, usw. So laufen Tests schnell,
deterministisch und ohne echte Infrastruktur.

Fakes werden überwiegend von Unit-Tests verwendet (dort ersetzt `UnitTestCase`
alle Utils durch Fakes). Aber auch Integration-Tests setzen Fakes gezielt ein:
Sie arbeiten zwar mit der echten Test-Datenbank, ersetzen aber externe Dienste wie
Netzwerk-Aufrufe durch Fakes (z.B. `FakeSolvFetcher` für den `SolvFetcher`), damit
die Tests deterministisch bleiben und nicht ins Internet gehen.

## Was gibt es?

- `Fake*Utils` (z.B. `FakeEntityManager`, `FakeDbUtils`, `FakeDateUtils`,
  `FakeEnvUtils`, `FakeHttpUtils`): Fakes für die Utils aus `src/Utils/`.
  `UnitTestCase` registriert sie in `WithUtilsCache`.
- `FakeEntityManager`: liefert für die Entities `Fake*Repository`-Instanzen.
- `tests/Fake/Entity/`: vordefinierte Test-Entities (`FakeUser`, `FakeMember`,
  `FakeNews`, ...). Jede Fake-Entity hat typischerweise `minimal()`, `empty()` und
  `maximal()`.
- `FakeMysqli`: Fake für die mysqli-Verbindung von `DbUtils`.

## Fake-Entities

Fake-Entities werden über `FakeEntity::getFake()` erzeugt und zwischengespeichert.
Mit `$fresh = true` erhält man eine neue Instanz, sonst dieselbe (referenzgleiche)
Instanz für denselben Aufruf. `FakeEntity::reset()` leert diesen Cache (passiert in
`UnitTestCase::setUp()`).

## Einen neuen Fake hinzufügen

- Utils: neue `Fake*Utils`-Klasse unter `tests/Fake/` anlegen und in
  `UnitTestCase::setUp()` in `WithUtilsCache::setAll([...])` registrieren.
- Entities: neue `Fake*`-Klasse unter `tests/Fake/Entity/` anlegen (analog zu den
  bestehenden) und bei Bedarf ein `Fake*Repository` ergänzen.
