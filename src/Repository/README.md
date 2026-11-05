# `Repository/`: Datenbank-Repositories

Repositories kapseln Datenbank-Abfragen. Die Ordner-Struktur spiegelt die von
[`../Entity/`](../Entity/) (z.B. `Users/UserRepository` für `Entity/Users/User`).

## Konventionen

- Alle Repositories erweitern [`Common/OlzRepository.php`](./Common/OlzRepository.php)
  (eine Doctrine `EntityRepository`).
- Die zugehörige Entity wird über `protected string $entityClass = X::class;`
  gesetzt.
- Eigene Abfragen als Methoden (z.B. `findBy...`, `get...`).
- Schreibende Methoden speichern Änderungen mit `persist()` und `flush()` (bzw. der
  Aufrufer flusht anschliessend).
