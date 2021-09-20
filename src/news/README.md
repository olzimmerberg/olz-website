# `news/`: Neuigkeiten (Ersatz für "Aktuell", "Galerie", "Kaderblog" und "Forum")

Mithilfe des [APIs](../api/README.md) soll hier eine neue Version für Berichte/Einträge aller Art entstehen.

## Felder der News-Einträge

- `owner_user`: Verantwortlicher Benutzer
- `owner_role`: Verantwortliche Benutzerrolle/gruppe
- `author` (optional): Pseudonym für den Urheber des Eintrags
- `author_user` (optional): Der Benutzer, der als Urheber angezeigt werden soll
- `author_role` (optional): Die Benutzerrolle, die als Urheber angezeigt werden soll (z.B. "Vorstand" oder "Trainings")
- `title`: Titel des Eintrags
- `teaser` (optional): Teaser (Kurztext) des Eintrags. Falls kein Teaser vorhanden ist, wird `text` automatisch zugeschnitten, um einen Teaser zu generieren.
- `content`: Inhalt des Eintrags
- `external_url`: Falls dies ein Eintrag ist, der ursprünglich auf einer anderen Website publiziert wurde, ist dies die URL, die auf den Original-Eintrag verlinkt.
- `tags`: Tags für diesen Eintrag (z.B.: "Bericht", "Forum", "Ausblick")
- `created_at`: Erstellungsdatum
- `created_by_user`: Benutzer, der den Eintrag erstellt hat
- `last_modified_at`: Änderungsdatum
- `publish_at`: Publizierungsdatum
- `termin_id`: Falls der News-Eintrag mit einem bestimmten Termin zu tun hat
- `on_off`: Ein/Aus-Schalter

Aus `author`, `author_user` und `author_group` wird der anzuzeigende Urheber ermittelt.

## Migration der Bilder

### Bisher

Bisher wurden die Bilder abgespeichert als `{$data_path}img/{$db_table}/{$id}/img/{$index}.jpg`.

z.B.
- `.../img/aktuell/1234/img/001.jpg`
- `.../img/aktuell/1234/img/002.jpg`
- `.../img/aktuell/1235/img/001.jpg`
- `.../img/galerie/3/img/001.jpg`
- `.../img/galerie/3/img/002.jpg`
- etc.

Zusätzlich wurden Thumbnails generiert: `{$data_path}img/{$db_table}/{$id}/thumb/{$index}_{$dimensions}.jpg`.

z.B.
- `.../img/aktuell/1234/thumb/001_80x60.jpg`
- `.../img/aktuell/1234/thumb/001_160x120.jpg`
- `.../img/aktuell/1234/thumb/002_80x60.jpg`
- `.../img/aktuell/1235/thumb/001_60x80.jpg`
- `.../img/galerie/3/thumb/001_80x60.jpg`
- `.../img/galerie/3/thumb/002_80x60.jpg`
- etc.

Eingebunden wurden Bilder als `<BILD{$index}>`.

z.B.
- `<BILD1>`
- `<BILD2>`

Bei Galerien wurden automatisch alle Bilder angezeigt, die im entsprechenden Ordner abgespeichert waren.

### Neu

Neu soll jeder Upload eine eindeutige ID (= Hash) haben. Bilder sollen abgespeichert werden als `{$data_path}img/news/{$id}/img/{$hash}.jpg`.

z.B.
- `.../img/news/1234/img/abcdefghijklmnopqrstuvwx.jpg`
- `.../img/news/1234/img/9TsaBhb4DvkpIrhJt4kjvhrO.jpg`
- `.../img/news/1235/img/76ffQmgAiCRv1HOTLdumQJIS.jpg`
- `.../img/news/3/img/bAKg1ext1rH9_e0h3ky5vN0f.jpg`
- `.../img/news/3/img/LFm4w-0p1ItH0FVReqS2SU4M.jpg`
- etc.

Thumnails sollen schon beim Upload, und nicht wie bisher mittels `image_tools.php` generiert werden!

z.B.
- `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_default.jpg` (default = max. Grösse 128)
- `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_160x120.jpg` (möglich für zukünftige Features)
- `.../img/news/1234/thumb/abcdefghijklmnopqrstuvwx_240.jpg` (möglich für zukünftige Features)
- `.../img/news/1234/thumb/9TsaBhb4DvkpIrhJt4kjvhrO_default.jpg`
- `.../img/news/1235/thumb/76ffQmgAiCRv1HOTLdumQJIS_default.jpg`
- `.../img/news/3/thumb/bAKg1ext1rH9_e0h3ky5vN0f_default.jpg`
- `.../img/news/3/thumb/LFm4w-0p1ItH0FVReqS2SU4M_default.jpg`
- etc.

Bei Galerien muss das Feld `image_ids` genutzt werden.

Die Unterscheidung, ob das alte oder das neue System verwendet werden soll, soll anhand des Feldes `image_ids` festgestellt werden.
