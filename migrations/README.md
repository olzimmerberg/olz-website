# `migrations/` Die Datenbank-Migrationen

Jedes Mal, wenn die Struktur der Datenbank verändert wird - typischerweise durch Änderungen in [`src/Entity`](../src/Entity/) - muss im gleichen Commit hier eine neue Migration erstellt werden:

- `php bin/console olz:db-diff`

Ausserdem müssen die Testdaten dann aktualisiert werden:

- `git checkout main`
- `php bin/console olz:db-reset structure`
- `git checkout -`
- `php bin/console olz:db-migrate`
- `php bin/console olz:db-dump`
