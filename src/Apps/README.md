# `Apps`

## Übersicht

Apps fügen sehr spezifische, weitere Funktionen zur OLZ-Website hinzu.

## Aufbau / Entwicklung

Jedes App benötigt typischerweise:

- Eine `Metadata.php`-Datei, mit einer Klasse, die [`BaseAppMetadata`](./BaseAppMetadata.php) implementiert
- Ein `README.md`, das die Funktion des Apps beschreibt
- Ein Icon, als `icon.svg` oder `icon.png`
- Eine Datei `<app>Controller.php`, mit einer Klasse, die `AbstractController` erweitert. Diese definiert die URLs, über die die App erreichbar ist (siehe [Symfony-Docs](https://symfony.com/doc/current/controller.html))
- API-Endpoints in `Endpoints`:
    - Implementierung in `Endpoints/<function>Endpoint.php`, mit einer Klasse, die `Olz\Api\OlzEndpoint` erweitert.
- Um im [OLZ-API](../Api/) zu erscheinen, müssen allfällige Endpoints in einer Datei `<App>Endpoints.php`, mit einer Klasse, die `BaseAppEndpoints` erweitert, registriert werden.
- Komponenten in `Components`:
    - Serverseitig generierter Inhalt in `Components/<component>/<component>.php`
    - React-Komponent oder Hooks für serverseitigen Inhalt in `Components/<component>/<component>.ts(x)`
    - SCSS-Style in `Components/<component>/<component>.scss`
    - `index.ts(x)`, das den clientseitigen Code aller Komponenten kombiniert
- Eintrittspunkt `index.ts(x)` (für webpack), falls clientseitiger Code (oder SCSS-Style) verwendet wird
