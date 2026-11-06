# `SystemTests/`: System-Tests

System-Tests prüfen die Website von aussen, grösstenteils als echte Browser-Tests
(Panther/WebDriver). Je nach Modus laufen sie gegen einen lokalen Dev-Server oder
gegen `staging`/`prod`. Einige Tests prüfen auch ohne Browser (z.B. DNS-Einträge,
HTTP-Header, Status-Endpunkt).

Die Tests dienen zusätzlich als "Screenshot-Tests": Mit `screenshot()` wird der
HTML-Zustand einer Seite festgehalten, und die CI zeigt Änderungen daran als Diff an
(siehe unten).

## Modi

Der Modus wird über die Umgebungsvariable `SYSTEM_TEST_MODE` gewählt und bestimmt die
Ziel-URL (`getTargetUrl()`). Einzelne Testmethoden lassen sich über das
`OnlyInModes`-Attribut auf bestimmte Modi einschränken.

| Modus | Ziel-URL |
| --- | --- |
| `dev` / `dev_rw` | `http://127.0.0.1:30270` |
| `staging` / `staging_rw` | `https://staging.olzimmerberg.ch` |
| `prod` | `https://olzimmerberg.ch` |
| `meta` | keine (Tests ohne Browser) |

`_rw` heisst "read-write": Nur in diesen Modi darf `resetDb()` aufgerufen werden.

## Ausführen

- Standard (Firefox, `dev`): `composer system_tests`
- Optionen:
  - Modus: `--dev` | `--dev_rw` | `--staging` | `--staging_rw` | `--prod` | `--meta`
  - Browser: `--firefox` (Default) | `--chrome`
  - `--no-build`: JavaScript-Build überspringen
  - Slice: z.B. `1/4` (Aufteilung der Suite auf mehrere Läufe, `SYSTEM_TEST_SLICE`)

Im `dev`-Modus setzt das Script die Datenbank zurück (`olz:db-reset full`), baut das
JavaScript (ausser mit `--no-build`) und startet den Dev-Server auf Port 30270. Die
Browser-Treiber (`geckodriver`/`chromedriver`) müssen auf dem `PATH` (oder unter
`./drivers`) verfügbar sein.

## Aufbau

- Basisklasse: `SystemTestCase` (`tests/SystemTests/Common/SystemTestCase.php`).
- Wichtige Hilfsmethoden:
  - `loadUrl()`, `getUrl()`, `getTargetUrl()`, `getTitle()`
  - `getClient()`, `getCrawler()`, `filter()`
  - `login()`, `click()`, `clear()`, `sendKeys()`
  - `waitABit()`, `waitFor()`, `waitUntil()`, `waitUntilGone()`, `waitForModal()`
  - `screenshot()`, `resetDb()`, `getHeaders()`

## Screenshots

`$this->screenshot('name')` speichert den HTML-Zustand der aktuellen Seite als
`screenshots/generated/{name}-{browser}.html` (pro Browser). Nicht-deterministischer
Inhalt (`data-lg-id`, `.test-flaky`) wird dabei entfernt. Screenshot-Namen müssen
über alle Tests hinweg eindeutig sein.

In der CI vergleicht `ci/screenshot-modifications.php` die Screenshots und stellt
Änderungen als Diff dar ("Screenshot modification tests").

## TODO: Offene Use Cases

- **Update user / E-Mail erneut verifizieren:** `ProfilTest::testProfilUpdateUser`
  deckt das Aktualisieren der Profildaten ab. Es fehlen aber das erneute Verifizieren
  der E-Mail nach einer Adressänderung und ein Screenshot davon.
- **E-Mail-Weiterleitung:** noch kein System-Test vorhanden.
