# `model/`: Daten-Modell

Hier ist definiert, was in der Datenbank gespeichert werden soll.
Beim Verändern dieser Modelle muss eine Datenbank-Migration erstellt werden,
falls sich die Datenbank-Struktur verändert hat, oder Daten transformiert
werden müssen:

`composer migrations diff`

## Gut zu wissen

### Keine Indexe mit `text` Feldern!

`unique=true` sollte nicht verwendet werden, zumindest nicht bei Feldern des
Typs `text`. Die Datenbank von unserem Hoster unterstützt das resultierende SQL
dann  nämlich nicht, und die Migration wird nicht ausgeführt.

### Views zur Analyse erstellen

- `CREATE OR REPLACE VIEW users_latest_signup AS SELECT * FROM users ORDER BY created_at DESC;`
- `CREATE OR REPLACE VIEW users_latest_change AS SELECT * FROM users ORDER BY last_modified_at DESC;`
- `CREATE OR REPLACE VIEW users_latest_login AS SELECT * FROM users ORDER BY last_login_at DESC;`
- `CREATE OR REPLACE VIEW counter_fuer_einsteiger AS SELECT * FROM counter WHERE page LIKE '%fuer_einsteiger.php%' ORDER BY date_range DESC, counter DESC;`
- `CREATE OR REPLACE VIEW telegram_links_latest AS SELECT u.username, tl.* FROM telegram_links tl LEFT JOIN users u ON (tl.user_id = u.id) ORDER BY tl.created_at DESC;`
- `CREATE OR REPLACE VIEW notification_subscriptions_latest AS SELECT u.username, ns.* FROM notification_subscriptions ns LEFT JOIN users u ON (ns.user_id = u.id) ORDER BY ns.created_at DESC;`
- `CREATE OR REPLACE VIEW auth_requests_latest_blocked AS SELECT * FROM auth_requests WHERE action='BLOCKED' ORDER BY timestamp DESC;`