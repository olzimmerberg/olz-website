# `fixtures/`: Fixtures für Dev-Server und Tests

Hier liegen die Dateien, mit denen der Dev-Server und die Tests mit
realistischen Daten befüllt werden. Bewusst ausserhalb von `src/`, da diese
Dateien nicht Teil des Anwendungscodes sind (sie werden nicht autogeloadet).

## Inhalt

- `sample-data/`: Beispiel-Dateien (PDF, Bilder, Schriften, XML, CSV), aus denen
  `DevDataUtils::addFiles()` den Datei-Baum des Dev-Servers bzw. der
  Integration-Tests generiert und die von den System-Tests als Upload verwendet
  werden.
- `db/`: Die generierten Datenbank-Dumps (`structure.sql`,
  `content.sql`). Sie werden von `olz:db-reset` verwendet, um die
  Entwicklungs-/Test-Datenbank schnell und deterministisch zu befüllen.

## Datenbank-Dumps aktualisieren

Die Dumps werden aus der aktuellen Datenbank generiert:

- `php bin/console olz:db-dump`

Sie müssen im selben Commit wie eine neue Migration (siehe
[`migrations/README.md`](../migrations/README.md)) aktualisiert werden. Der
Integration-Test `DevDataUtilsIntegrationTest` prüft, dass die Dumps zur
aktuellen Migration passen.
