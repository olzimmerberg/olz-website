<?php

namespace Olz\Components\OtherPages\OlzFragenUndAntworten;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Roles\Role;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Termine\Components\OlzTermineTicker\OlzTermineTicker;

class OlzFragenUndAntworten extends OlzComponent {
    public static $title = "Fragen & Antworten";
    public static $description = "Antworten auf die wichtigsten Fragen rund um den OL und die OL Zimmerberg.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $news_filter_utils = NewsFilterUtils::fromEnv();
        $role_repo = $entityManager->getRepository(Role::class);
        $buessli_role = $role_repo->getPredefinedRole(PredefinedRole::Buessli);
        $aktuariat_role = $role_repo->getPredefinedRole(PredefinedRole::Aktuariat);
        $nachwuchs_role = $role_repo->getPredefinedRole(PredefinedRole::Nachwuchs);
        $forum_url = $news_filter_utils->getUrl(['format' => 'forum']);
        $galerie_url = $news_filter_utils->getUrl(['format' => 'galerie']);
        $kaderblog_url = $news_filter_utils->getUrl(['format' => 'kaderblog']);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-right'>";

        $out .= "<h3>Ansprechperson</h3>
        <div style='padding:0px 10px 0px 10px;'>";
        $nachwuchs_assignees = $nachwuchs_role->getUsers();
        foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
            $out .= OlzUserInfoCard::render(['user' => $nachwuchs_assignee]);
        }
        $out .= "</div>";

        $out .= "</div>
        <div class='content-middle'>";

        $out .= <<<ZZZZZZZZZZ
        <h1>Fragen & Antworten (FAQ)</h1>
        <h3>Was ist OL?</h3>
        <div>
        Das erklären wir dir in unserem kurzen Youtube Video:<br><br>
        <iframe width='560' height='315' src='https://www.youtube-nocookie.com/embed/JVL0vgcnM6c' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen class='test-flaky'></iframe>
        </div>
        <h3>Was tun?</h3>
        <div>
        Am besten kommst du in eines unserer <b>Trainings</b> (mit <a href='https://youtu.be/PjsDAQM1kxA' target='_blank' class='linkext'>Youtube Video</a> zur Vorbereitung).<br>
        Jährlich organisieren wir ein <b>OL-Lager</b> und ein <b>Tageslager</b> für Kinder und Jugendliche. Wann genau diese stattfinden, verraten wir dir bei den <a href='{$code_href}termine' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Terminen</a>.
        </div>
        <br>
        <h3>Wann finden diese Trainings statt?</h3>
        <div>
        Alle Anlässe und damit auch die Trainings werden bei uns auf der <b><a href='{$code_href}termine' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Termine-Seite</a></b> bekannt gegeben. <br>Auf der rechten Seite bei den <a href='{$code_href}termine' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Terminen</a> findest du auch diese <b>Übersicht über unsere Trainings</b>:
        </div>
        <div style='border:1px solid black; margin:5px; padding:0px;'><h4 class='tablebar'>Übersicht über unsere Trainings</h4>
        ZZZZZZZZZZ;
        $out .= OlzEditableText::render(['olz_text_id' => 1]);
        $out .= <<<ZZZZZZZZZZ
        </div>
        <div>
        Wir haben dir hier noch die nächsten 3 Trainings herausgesucht. Diese findest du natürlich auch auf der <a href='{$code_href}termine' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Termine-Seite</a>.
        </div>
        <div style='border:1px solid black; margin:5px; padding:0px;'>
        ZZZZZZZZZZ;
        $out .= OlzTermineTicker::render([
            "eintrag_laenge" => 80,
            "eintrag_anzahl" => 3,
            "titel" => "Die nächsten 3 Trainings",
            "sql_where" => " AND typ LIKE '%training%'",
            "heute_highlight" => false,
        ]);
        $out .= <<<ZZZZZZZZZZ
        </div>
        <br>
        <h3>Wo finden die OL-Trainings statt?</h3>
        <div>
        Meistens in der Region Zimmerberg, auf <b><a href='{$code_href}karten' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>unseren Karten</a></b>.
        </div>
        <br>
        <h3>Wie reise ich zu einem Training?</h3>
        <div>
        Entweder du kommst zu Fuss, mit dem Velo, mit dem eigenen Auto, mit einer Fahrgemeinschaft, oder mit unserem <b>Clubbüssli</b>. <br>
        Das Büssli fährt an <b>jedes Training</b> und nimmt Jugendliche aus Wädenswil, Richterswil und Horgen mit. <br>
        Für die Jugendlichen aus der Region Adliswil/Langnau gibt es jeweils eine Fahrgemeinschaft. <br>
        Fürs Büssli anmelden kannst du dich bei:
        <div style='padding-left:50px;'>
        ZZZZZZZZZZ;
        $buessli_assignees = $buessli_role->getUsers();
        foreach ($buessli_assignees as $buessli_assignee) {
            $out .= OlzUserInfoCard::render(['user' => $buessli_assignee]);
        }
        $out .= <<<ZZZZZZZZZZ
        </div>
        </div>
        <br>
        <h3>Wie reise ich zum OL?</h3>
        <div>
        Bei manchen Läufen wird im <b><a href='{$forum_url}' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Forum</a></b> ein <b>Zug</b> bestimmt, mit dem die meisten anreisen werden.<br>
        Unser <b>Clubbüssli</b> fährt auch zu manchen Anlässen. Ob es zum nächsten OL fährt, erfährst du bei:
        <div style='padding-left:50px;'>
        ZZZZZZZZZZ;
        $buessli_assignees = $buessli_role->getUsers();
        foreach ($buessli_assignees as $buessli_assignee) {
            $out .= OlzUserInfoCard::render(['user' => $buessli_assignee]);
        }
        $out .= <<<ZZZZZZZZZZ
        </div>
        </div>
        <br>
        <h3>Wie erkenne ich andere OL Zimmerberg Mitglieder?</h3>
        <div>
        An der guten Stimmung und an unserem grün-gelb-schwarzen Dress, das auch tausendfach in den <b><a href='{$galerie_url}' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Galerien</a></b> zu sehen ist.
        </div>
        <br>
        <h3>Wie werde ich OL Zimmerberg Mitglied?</h3>
        <div>
        Melde dich bei:
        <div style='padding-left:50px;'>
        ZZZZZZZZZZ;
        $aktuariat_assignees = $aktuariat_role->getUsers();
        foreach ($aktuariat_assignees as $aktuariat_assignee) {
            $out .= OlzUserInfoCard::render(['user' => $aktuariat_assignee]);
        }
        $out .= <<<ZZZZZZZZZZ
        </div>
        </div>
        <br>
        <h3>Gibt es auch schnelle Läufer in der OL Zimmerberg?</h3>
        <div>
        Ja. Sie schreiben sogar manchmal Beiträge im <b><a href='{$kaderblog_url}' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Kaderblog</a></b>.
        </div>
        <br>
        <h3>Wo kann ich Berichte von vergangenen Anlässen nachlesen?</h3>
        <div>
        Auf der <b><a href='{$code_href}news' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>News-Seite</a></b>.
        </div>
        <br>
        <h3>Wer ist im Vorstand der OL Zimmerberg?</h3>
        <div>
        Porträts unseres Vorstandes sind auf der <b><a href='{$code_href}verein' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Vereins-Seite</a></b> zu finden.
        </div>
        <br>
        <h3 id='forumsregeln'>Welche Regeln gelten für das Forum?</h3>
        <div>
        ZZZZZZZZZZ;
        $out .= OlzEditableText::render(['olz_text_id' => 4]);
        $out .= <<<'ZZZZZZZZZZ'
        </div>
        <br>
        <h3 id='weshalb-telegram-push'>Weshalb verwendet ihr Telegram für den Nachrichten-Push?</h3>
        <div>
        Das ist natürlich eine sehr berechtigte Frage, denn die Chat-App Telegram steht oft datenschutztechnisch in der Kritik, und wird auch politisch teilweise als nicht neutral wahrgenommen.
        </div>
        <div>
        Die einfache Antwort ist, dass kein anderes Chat-App einen solchen automatisierten Chat so einfach und kostenfrei anbietet. Um genau zu sein:
        <ul class='bullet-list'>
        <li>Threema hat zwar eine solche Funktionalität, sie ist aber kompliziert zu implementieren und kostenpflichtig: Es kostet sowohl für uns jede Nachricht als auch das App für den Nutzer.</li>
        <li>WhatsApp hat zwar die "WhatsApp Business API" mit einer ähnichen Funktionalität, diese ist aber ausdrücklich eher an Grossunternehmen gerichtet, und somit auch kostenpflichtig.</li>
        <li>Signal bietet zwar auch eine Möglichkeit, automatische Nachrichten zu schreiben, aber auch diese ist kompliziert und nur mit weiteren Kosten zu implementieren.</li>
        </ul>
        Die Website-Entwickler danken für euer Verständnis.
        </div>
        <br>
        <h3 id='benutzername-email-herausfinden'>Wie finde ich meinen Benutzernamen bzw. E-Mail heraus?</h3>
        <div>
        <ul class='bullet-list'>
        <li>Erhälst du den Newsletter? Dann ist es die E-Mail Adresse, an welche der Newsletter versendet wird.</li>
        <li>Hast du Telegram verlinkt? Dann schreib deinem OLZ Bot die Nachricht <span style='font-family: monospace;'>/ich</span>, und er wird dir deinen Benutzernamen und deine E-Mail Adresse mitteilen.</li>
        <li>Wenn du hier angelangt bist, bleibt leider nur noch raten, welche E-Mail Adresse du verwendet haben könntest.</li>
        </ul>
        </div>
        <br>
        <h3 id='neues-familienmitglied-erstellen'>Wie kann ich ein OLZ-Konto für ein Familienmitglied erstellen?</h3>
        <div>
        <ul class='bullet-list'>
        <li>Stelle sicher, dass du eingeloggt bist</li>
        <li>Gehe auf dein Profil (OLZ-Konto-Menu rechts oben > Profil)</li>
        <li>Wähle "Neues Familienmitglied erstellen"</li>
        <li>Formular ausfüllen und abschicken (Hinweis: Im Gegensatz zum Hauptkonto dürfen E-Mail und Passwort leer bleiben)</li>
        <li>Nun hast du im OLZ-Konto-Menu rechts oben die Möglichkeit, zwischen deinem Hauptkonto und dem Kind-Konto hin- und herzuwechseln</li>
        </ul>
        </div>
        <br>
        <h3>Wen kann ich fragen, wenn ich weitere Fragen habe?</h3>
        <div style='padding-left:50px;'>
        ZZZZZZZZZZ;
        $buessli_assignees = $buessli_role->getUsers();
        foreach ($buessli_assignees as $buessli_assignee) {
            $out .= OlzUserInfoCard::render(['user' => $buessli_assignee]);
        }
        $out .= "</div>";

        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
