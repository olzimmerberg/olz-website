# `migrations/` Die Datenbank-Migrationen

Jedes Mal, wenn die Struktur der Datenbank verändert wird - typischerweise durch Änderungen in [`src/Entity`](../src/Entity/) - muss im gleichen Commit hier eine neue Migration erstellt werden:

- `php bin/console make:migration`

Ausserdem müssen die Testdaten dann aktualisiert werden:

- `git checkout main`
- `./run.sh`
- `http://127.0.0.1:30270/tools.php/reset` aufrufen
- `git checkout -`
- `php bin/console doctrine:migrations:migrate`
- `./run.sh`
- `http://127.0.0.1:30270/tools.php/dump` aufrufen