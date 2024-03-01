# `Api/`: Das OLZ-API (Application Programming Interface)

Wir haben ein auf Remote-Procedure-Call (RPC) basiertes, typisiertes API (mit [`php-typescript-api`](https://github.com/allestuetsmerweh/php-typescript-api)):

- Das API wird in [`OlzApi.php`](./OlzApi.php) spezifiziert
- Jeder RPC wird in einer Datei `<Funktion>Endpoint.php` implementiert, mit einer Klasse, die von einem Endpoint (z.B. [`Olz\Api\OlzEndpoint`](./OlzEndpoint.php)) erbt.
    - Für generelle RPCs [`Olz\Api\Endpoints\<Funktion>Endpoint`](./Endpoints/)
    - Für modul-spezifische RPCs `Olz\<Modul>\Endpoints\<Funktion>Endpoint` (z.B. für [`News`-Modul](../News/Endpoints/))
    - Für [App](../Apps/)-spezifische RPCs `Olz\Apps\<App>\Endpoints` (z.B. für [Resultate (`Results`)](../Apps/Results/Endpoints/))
- Der Client-Code wird bei jedem Build (z.B. `npm run webpack-build` oder `composer run`) in [/client`](./client/) generiert, und kann mit `olzApi.call(...)` aufgerufen werden.
