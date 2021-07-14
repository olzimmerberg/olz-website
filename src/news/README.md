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