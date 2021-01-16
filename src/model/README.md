# `model/`: Daten-Modell

Hier ist definiert, was in der Datenbank gespeichert werden soll.
Beim Verändern dieser Modelle muss eine Datenbank-Migration erstellt werden,
falls sich die Datenbank-Struktur verändert hat, oder Daten transformiert
werden müssen:

`./migrations.sh diff`

## Gut zu wissen

### Keine Indexe mit `text` Feldern!

`unique=true` sollte nicht verwendet werden, zumindest nicht bei Feldern des
Typs `text`. Die Datenbank von unserem Hoster unterstützt das resultierende SQL
dann  nämlich nicht, und die Migration wird nicht ausgeführt.
