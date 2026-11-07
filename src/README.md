# `src/`: Quellcode

Übersicht der Module unter `src/`:

## Features / Domänen

Die Feature-Module sind typischerweise gleich aufgebaut:

- `Components/`: UI (serverseitige/React-Komponenten sowie SCSS).
- `Endpoints/`: OLZ-API-Endpoints (siehe [`Api/`](./Api/)).
- `Utils/`: Hilfsfunktionen.
- `Controller/`: Routing (`Controller/<Modul>Controller.php`).

Nicht jedes Modul nutzt alle Ordner.

- [`Anniversary/`](./Anniversary/): Jahresrückblick mit Laufdaten
- [`Apps/`](./Apps/): zusätzliche, spezifische Funktionen (siehe
  [`Apps/README.md`](./Apps/README.md))
- [`Captcha/`](./Captcha/): Captcha / Spam-Schutz
- [`Common/`](./Common/): gemeinsam genutzte Bausteine
- [`Faq/`](./Faq/): Fragen und Antworten
- [`Karten/`](./Karten/): OL-Karten
- [`News/`](./News/): Neuigkeiten (Aktuell, Galerie, Kaderblog, Forum)
- [`Roles/`](./Roles/): Rollen und Verein/Organigramm
- [`Service/`](./Service/): Service-Seite (Downloads, Links)
- [`Snippets/`](./Snippets/): editierbare Text-Snippets
- [`Startseite/`](./Startseite/): Startseite mit Kacheln
- [`Suche/`](./Suche/): Suche
- [`Termine/`](./Termine/): Termine und Kalender
- [`Users/`](./Users/): Benutzer

## Applikations-Grundgerüst

- [`Api/`](./Api/): OLZ-API
- [`Command/`](./Command/): ([Symfony](https://symfony.com/doc/current/console.html)) Console-Commands
- [`Components/`](./Components/): wiederverwendbare visuelle Komponenten (TODO: move to `Common/Components/`)
- [`Constants/`](./Constants/): Konstanten
- [`Entity/`](./Entity/): ([Symfony/Doctrine](https://symfony.com/doc/current/doctrine.html)) Daten-Modell
- [`EventListener/`](./EventListener/): ([Symfony](https://symfony.com/doc/current/event_dispatcher.html)) Event-Listener
- [`Exceptions/`](./Exceptions/): eigene Exceptions
- [`Fetchers/`](./Fetchers/): externe Daten-Fetcher
- [`Message/`](./Message/): ([Symfony](https://symfony.com/doc/current/messenger.html)) Messenger-Nachrichten
- [`MessageHandler/`](./MessageHandler/): ([Symfony](https://symfony.com/doc/current/messenger.html)) Messenger-Handler
- [`Parsers/`](./Parsers/): Parser
- [`Repository/`](./Repository/): ([Symfony/Doctrine](https://symfony.com/doc/current/doctrine.html)) Datenbank-Repositories
