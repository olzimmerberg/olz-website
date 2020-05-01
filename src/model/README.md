# OLZ Data Model

## Caveats

### `unique=true`

Do not use `unique=true`, at least not on `text` type fields.
The Hoststar Database can not handle the resulting SQL.
