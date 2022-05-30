<?php

function die_with_http_error($http_status_code) {
    http_response_code($http_status_code);

    require_once __DIR__.'/components/page/olz_header/olz_header.php';
    echo olz_header([
        'title' => "Fehler",
    ]);

    echo <<<'ZZZZZZZZZZ'
    <div id='content_rechts'>
        <h2>&nbsp;</h2>
        <img src='icns/schilf.jpg' style='width:98%;' alt=''>
    </div>
    <div id='content_mitte'>
        <h2>Fehler 404: Die gewünschte Seite konnte nicht gefunden werden.</h2>
        <p><b>Hier bist du voll im Schilf!</b></p>
        <p>Kein Posten weit und breit.</p>
        <p>Vielleicht hast du falsch abgezeichnet? Oder der Posten wurde bereits abgeräumt!</p>
        <p>Aber keine Bange, <a href='startseite.php' class='linkint'>hier kannst du dich wieder auffangen.</a></p>
        <p>Und wenn du felsenfest davon überzeugt bist, dass der Posten hier sein <b>muss</b>, dann hat wohl der Postensetzer einen Fehler gemacht und sollte schläunigst informiert werden:
        <script type='text/javascript'>
            MailTo("website", "olzimmerberg.ch", "Postensetzer", "Fehler%20404%20OLZ");
        </script></p>
    </div>
    ZZZZZZZZZZ;

    require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
    echo olz_footer();
    exit('');
}
