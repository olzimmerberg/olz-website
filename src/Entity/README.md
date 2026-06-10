# `Entity/`: Daten-Modell

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


```
CREATE OR REPLACE VIEW users_latest_signup AS
    SELECT *
    FROM users
    ORDER BY created_at DESC;
```

```
CREATE OR REPLACE VIEW users_latest_change AS
    SELECT *
    FROM users
    ORDER BY last_modified_at DESC;
```

```
CREATE OR REPLACE VIEW users_latest_login AS
    SELECT *
    FROM users
    ORDER BY last_login_at DESC;
```

```
CREATE OR REPLACE VIEW counter_angebot AS
    SELECT date_range, page, counter
    FROM counter
    WHERE page LIKE '/angebot%'
    ORDER BY date_range DESC, counter DESC;
```

```
CREATE OR REPLACE VIEW counter_fuer_einsteiger AS
    SELECT date_range, page, counter
    FROM counter
    WHERE page LIKE '%fuer_einsteiger%'
    ORDER BY date_range DESC, counter DESC;
```

```
CREATE OR REPLACE VIEW counter_high_latency AS
    SELECT date_range, page, counter, latency_avg_ms, latency_num
    FROM counter
    WHERE latency_num > '10'
    ORDER BY date_range DESC, latency_avg_ms DESC;
```

```
CREATE OR REPLACE VIEW counter_startseite AS
    SELECT date_range, page, counter
    FROM counter
    WHERE page LIKE '%von=st-%'
    ORDER BY date_range DESC, counter DESC;
```

```
CREATE OR REPLACE VIEW counter_startseite_position AS 
    SELECT
        date_range,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-0%') AS st_nw_0,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-1%') AS st_nw_1,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-2%') AS st_nw_2,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-3%') AS st_nw_3,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-4%') AS st_nw_4,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-nw-5%') AS st_nw_5,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-0%') AS st_tr_0,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-1%') AS st_tr_1,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-2%') AS st_tr_2,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-3%') AS st_tr_3,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-4%') AS st_tr_4,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-5%') AS st_tr_5,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-6%') AS st_tr_6,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-tr-7%') AS st_tr_7,
        (SELECT SUM(c1.counter) FROM counter c1 WHERE c1.date_range = c.date_range AND c1.page LIKE '%von=st-dl%') AS st_dl
    FROM counter c
    WHERE c.page = '/'
    ORDER BY c.date_range DESC;
```

```
CREATE OR REPLACE VIEW counter_von AS
    SELECT date_range, page, counter
    FROM counter
    WHERE page LIKE '%von=%'
    ORDER BY date_range DESC, counter DESC;
```

```
CREATE OR REPLACE VIEW telegram_links_latest AS
    SELECT u.username, tl.*
    FROM telegram_links tl
        LEFT JOIN users u ON (tl.user_id = u.id)
    ORDER BY tl.created_at DESC;
```

```
CREATE OR REPLACE VIEW notification_subscriptions_latest AS
    SELECT u.username, ns.*
    FROM notification_subscriptions ns
        LEFT JOIN users u ON (ns.user_id = u.id)
    ORDER BY ns.created_at DESC;
```

```
CREATE OR REPLACE VIEW auth_requests_latest_blocked AS
    SELECT *
    FROM auth_requests
    WHERE action='BLOCKED'
    ORDER BY timestamp DESC;
```
