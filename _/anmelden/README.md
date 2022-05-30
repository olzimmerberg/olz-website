# Anmelden

Online-Anmeldung für Anlässe, Trainings, Weekends, etc.

## Dokumentation

### Infos (=Felder) einer Registrierung (=Formular) (`infos`)

Sind ein Array folgender Form:

- `type`: Typ des Felds
    - *zum automatisch Ausfüllen (OLZ-Konto-Daten):*
        - `email`: E-Mail-Adresse
        - `firstName`: Vorname
        - `lastName`: Nachname
        - `gender`: Geschlecht (z.B. für Rangliste)
        - `street`: Adresse (sparsam benutzen!)
        - `postalCode`: PLZ (z.B. für Gemeindeduell o.ä.)
        - `city`: Ort (z.B. für Rangliste)
        - `region`: Region, Kanton (sparsam benutzen!)
        - `countryCode`: Land (sparsam benutzen!)
        - `birthdate`: Geburtsdatum (z.B. für Rangliste oder für SBB-Billette)
        - `phone`: Telefonnummer (z.B. für Notfälle)
    - *weitere:*
        - `string`: Text, der vom Benutzer ausgefüllt werden soll
        - `enum`: Optionen-Auswahl-Feld, das vom Benutzer ausgefüllt werden soll (z.B. "Vegan"/"Vegi"/"Fleisch")
        - `reservation`: Der Benutzer kann eine Option aus vorgegebenen Optionen auswählen, wobei jede Option (pro Registrierungsformular) nur von einem Benutzer gewählt werden kann (z.B. Reservierung eines Schlafplatzes, eines Parkplatzes, oder einer Startzeit)
- `is_optional`: Kann das Feld leer gelassen werden?
- `title`: Benutzer-lesbarer Titel des Felds
- `description`: Benutzer-lesbare Beschreibung des Felds (mit weiteren Informationen, Anweisungen für Spezialfälle, etc.)
- `options`: (nur für Typen `reservation` und `enum` relevant) Die zur Verfügung stehenden Optionen


### Feld-Werte einer Buchung (`values`)

Sind ein Dictionary folgender Form:

- Der Schlüssel ist von der Form: `<field-title>-<field-index>-<form-modification-date>`.
  Der Grund für dieses Format ist, dass wir kein komplettes Chaos haben, wenn das Registrierungsformular abgeändert wird, nachdem schon Registrierungen eingegangen sind.
- Die Werte sind je nach Feld-Typ verschieden:
    - *zum automatisch Ausfüllen (OLZ-Konto-Daten):*
        - `email`: `string`
        - `firstName`: `string`
        - `lastName`: `string`
        - `gender`: `'M'|'F'|'O'`
        - `street`: `string`
        - `postalCode`: `string`
        - `city`: `string`
        - `region`: `string`
        - `countryCode`: `string` (zweistelliger Code)
        - `birthdate`: `string` (`YYYY-MM-DD`)
        - `phone`: `string`
    - *weitere:*
        - `string`: `string`
        - `enum`: `string`
        - `reservation`: `string`
