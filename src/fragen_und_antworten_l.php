<?php

// =============================================================================
// Die Informationsseite für Anfänger, Einsteiger, Neulinge.
// =============================================================================

require_once __DIR__.'/components/users/olz_user_info_card/olz_user_info_card.php';
require_once __DIR__.'/model/Role.php';
require_once __DIR__.'/model/RoleRepository.php';
require_once __DIR__.'/model/User.php';
require_once __DIR__.'/model/UserRepository.php';
require_once __DIR__.'/tickers.php';

$role_repo = $entityManager->getRepository(Role::class);
$buessli_role = $role_repo->findOneBy(['username' => 'buessli']);
$aktuariat_role = $role_repo->findOneBy(['username' => 'aktuariat']);

echo "
<h1>Fragen & Antworten (FAQ)</h1>
<h3>Was ist OL?</h3>
<div>
Das erklären wir dir in unserem kurzen Youtube Video:<br><br>
<iframe width='560' height='315' src='https://www.youtube-nocookie.com/embed/JVL0vgcnM6c' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen class='test-flaky'></iframe>
</div>
<h3>Was tun?</h3>
<div>
Am besten kommst du in eines unserer <b>Trainings</b> (mit <a href='https://youtu.be/PjsDAQM1kxA' target='_blank' class='linkext'>Youtube Video</a> zur Vorbereitung).<br>
Jährlich organisieren wir ein <b>OL-Lager</b> und ein <b>Tageslager</b> für Kinder und Jugendliche. Wann genau diese stattfinden, verraten wir dir bei den <a href='termine.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Terminen</a>.
</div>
<br>
<h3>Wann finden diese Trainings statt?</h3>
<div>
Alle Anlässe und damit auch die Trainings werden bei uns auf der <b><a href='termine.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Termine-Seite</a></b> bekannt gegeben. <br>Auf der rechten Seite bei den <a href='termine.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Terminen</a> findest du auch diese <b>Übersicht über unsere Trainings</b>:
</div>
<div style='border:1px solid black; margin:5px; padding:0px;'><h4 class='tablebar'>Übersicht über unsere Trainings</h4>";
echo get_olz_text(1);
echo "</div>
<div>
Wir haben dir hier noch die nächsten 3 Trainings herausgesucht. Diese findest du natürlich auch auf der <a href='termine.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Termine-Seite</a>.
</div>
<div style='border:1px solid black; margin:5px; padding:0px;'>";
termine_ticker([
    "eintrag_laenge" => 80,
    "eintrag_anzahl" => 3,
    "titel" => "Die nächsten 3 Trainings",
    "sql_where" => " AND typ LIKE '%training%'",
    "heute_highlight" => false,
]);
echo "</div>
<br>
<h3>Wo finden die OL-Trainings statt?</h3>
<div>
Meistens in der Region Zimmerberg, auf <b><a href='karten.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>unseren Karten</a></b>.
</div>
<br>
<h3>Wie reise ich zu einem Training?</h3>
<div>
Entweder du kommst zu Fuss, mit dem Velo, mit dem eigenen Auto, mit einer Fahrgemeinschaft, oder mit unserem <b>Clubbüssli</b>. <br>
Das Büssli fährt an <b>jedes Training</b> und nimmt Jugendliche aus Wädenswil, Richterswil und Horgen mit. <br>
Für die Jugendlichen aus der Region Adliswil/Langnau gibt es jeweils eine Fahrgemeinschaft. <br>
Fürs Büssli anmelden kannst du dich bei:
<div style='padding-left:50px;'>";
$buessli_assignees = $buessli_role->getUsers();
foreach ($buessli_assignees as $buessli_assignee) {
    echo olz_user_info_card($buessli_assignee);
}
echo "</div>
</div>
<br>
<h3>Wie reise ich zum OL?</h3>
<div>
Bei manchen Läufen wird im <b><a href='forum.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Forum</a></b> ein <b>Zug</b> bestimmt, mit dem die meisten anreisen werden.<br>
Unser <b>Clubbüssli</b> fährt auch zu manchen Anlässen. Ob es zum nächsten OL fährt, erfährst du bei:
<div style='padding-left:50px;'>";
$buessli_assignees = $buessli_role->getUsers();
foreach ($buessli_assignees as $buessli_assignee) {
    echo olz_user_info_card($buessli_assignee);
}
echo "</div>
</div>
<br>
<h3>Wie erkenne ich andere OL Zimmerberg Mitglieder?</h3>
<div>
An der guten Stimmung und an unserem grün-gelb-schwarzen Dress, das auch tausendfach in den <b><a href='galerie.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Galerien</a></b> zu sehen ist.
</div>
<br>
<h3>Wie werde ich OL Zimmerberg Mitglied?</h3>
<div>
Melde dich bei:
<div style='padding-left:50px;'>";
$aktuariat_assignees = $aktuariat_role->getUsers();
foreach ($aktuariat_assignees as $aktuariat_assignee) {
    echo olz_user_info_card($aktuariat_assignee);
}
echo "</div>
</div>
<br>
<h3>Gibt es auch schnelle Läufer in der OL Zimmerberg?</h3>
<div>
Ja. Sie schreiben sogar manchmal Beiträge im <b><a href='blog.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Kaderblog</a></b>.
</div>
<br>
<h3>Wo kann ich Berichte von vergangenen Anlässen nachlesen?</h3>
<div>
Auf der <b><a href='aktuell.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Aktuell-Seite</a></b>.
</div>
<br>
<h3>Wer ist im Vorstand der OL Zimmerberg?</h3>
<div>
Porträts unseres Vorstandes sind auf der <b><a href='verein.php' onmouseover='highlight_menu(event)' onmouseout='unhighlight_menu(event)' class='linkint'>Vereins-Seite</a></b> zu finden.
</div>
<br>
<h3>Wen kann ich fragen, wenn ich weitere Fragen habe?</h3>
<div style='padding-left:50px;'>";
$buessli_assignees = $buessli_role->getUsers();
foreach ($buessli_assignees as $buessli_assignee) {
    echo olz_user_info_card($buessli_assignee);
}
echo "</div>";
