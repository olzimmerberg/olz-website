<?php

// =============================================================================
// Die Informationsseite für Anfänger, Einsteiger, Neulinge.
// =============================================================================

require_once __DIR__.'/model/Role.php';
require_once __DIR__.'/model/RoleRepository.php';
require_once __DIR__.'/model/User.php';
require_once __DIR__.'/model/UserRepository.php';
require_once __DIR__.'/components/users/olz_user_info_card/olz_user_info_card.php';

?>

<script type='text/javascript'>

function highlight_menu(page) {
    var menuContainerElem = document.getElementById('menu-container');
    var menuContainerStyle = window.getComputedStyle(menuContainerElem);
    var menuContainerOpacity = menuContainerStyle.getPropertyValue('opacity');
    if (menuContainerOpacity < 0.5) {
        return;
    }
    var elem = document.getElementById("menu_a_page"+page);
    var rect = elem.getBoundingClientRect();
    var pointer = document.createElement("img");
    pointer.style.pointerEvents = "none";
    pointer.style.position = "fixed";
    pointer.style.zIndex = "1000";
    pointer.style.top = (rect.top+rect.height/2-50)+"px";
    pointer.style.left = (rect.left+rect.width)+"px";
    pointer.style.height = "100px";
    pointer.style.border = "0px";
    pointer.src = "icns/arrow_red.svg";
    pointer.id = "highlight_menu_"+page;
    document.documentElement.appendChild(pointer);
    window.setTimeout("highlight_menu_ani("+page+", 0)", 100);
}
function highlight_menu_ani(page, step) {
    if (step==8) step = 0;
    var elem = document.getElementById("highlight_menu_"+page);
    if (!elem) return;
    elem.style.marginLeft = (Math.sin(step*2*3.1415/8)*4)+"px";
    window.setTimeout("highlight_menu_ani("+page+", "+(step+1)+")", 100);
}
function unhighlight_menu(page) {
    var elem = document.getElementById("highlight_menu_"+page);
    if (elem) {
        elem.parentElement.removeChild(elem);
    }
}
</script>

<?php

$role_repo = $entityManager->getRepository(Role::class);
$buessli_role = $role_repo->findOneBy(['username' => 'buessli']);
$aktuariat_role = $role_repo->findOneBy(['username' => 'aktuariat']);

echo "
<p style='text-align:center;background-color:#D4E7CE;padding-top:10px;padding-bottom:10px;margin-bottom:10px;border-bottom:1px solid #007521;border-top:1px solid #007521;'>Du bist neu beim OL oder in unserem Klub? <br><b>Dann ist diese Seite genau für dich!</b></p>
<h3>Was ist OL?</h3>
<div>
Das erklären wir dir in unserem kurzen Youtube Video:<br><br>
<iframe width='560' height='315' src='https://www.youtube-nocookie.com/embed/JVL0vgcnM6c' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen class='test-flaky'></iframe>
</div>
<h3>Was tun?</h3>
<div>
Am besten kommst du in eines unserer <b>Trainings</b> (mit <a href='https://youtu.be/PjsDAQM1kxA' target='_blank' class='linkext'>Youtube Video</a> zur Vorbereitung).<br>
Jährlich organisieren wir ein <b>OL-Lager</b> und ein <b>Tageslager</b> für Kinder und Jugendliche. Wann genau diese stattfinden, verraten wir dir bei den <a href='?page=3' onmouseover='highlight_menu(3)' onmouseout='unhighlight_menu(3)' class='linkint'>Terminen</a>.
</div>
<br>
<h3>Wann finden diese Trainings statt?</h3>
<div>
Alle Anlässe und damit auch die Trainings werden bei uns auf der <b><a href='?page=3' onmouseover='highlight_menu(3)' onmouseout='unhighlight_menu(3)' class='linkint'>Termine-Seite</a></b> bekannt gegeben. <br>Auf der rechten Seite bei den <a href='?page=3' onmouseover='highlight_menu(3)' onmouseout='unhighlight_menu(3)' class='linkint'>Terminen</a> findest du auch diese <b>Übersicht über unsere Trainings</b>:
</div>
<div style='border:1px solid black; margin:5px; padding:0px;'><h4 class='tablebar'>Übersicht über unsere Trainings</h4>";
olz_text_insert(1);
echo "</div>
<div>
Wir haben dir hier noch die nächsten 3 Trainings herausgesucht. Diese findest du natürlich auch auf der <a href='?page=3' onmouseover='highlight_menu(3)' onmouseout='unhighlight_menu(3)' class='linkint'>Termine-Seite</a>.
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
Meistens in der Region Zimmerberg, auf <b><a href='?page=12' onmouseover='highlight_menu(12)' onmouseout='unhighlight_menu(12)' class='linkint'>unseren Karten</a></b>.
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
Bei manchen Läufen wird im <b><a href='?page=5' onmouseover='highlight_menu(5)' onmouseout='unhighlight_menu(5)' class='linkint'>Forum</a></b> ein <b>Zug</b> bestimmt, mit dem die meisten anreisen werden.<br>
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
An der guten Stimmung und an unserem grün-gelb-schwarzen Dress, das auch tausendfach in den <b><a href='?page=4' onmouseover='highlight_menu(4)' onmouseout='unhighlight_menu(4)' class='linkint'>Galerien</a></b> zu sehen ist.
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
Ja. Sie schreiben sogar manchmal Beiträge im <b><a href='?page=7' onmouseover='highlight_menu(7)' onmouseout='unhighlight_menu(7)' class='linkint'>Kaderblog</a></b>.
</div>
<br>
<h3>Wo kann ich Berichte von vergangenen Anlässen nachlesen?</h3>
<div>
Auf der <b><a href='?page=2' onmouseover='highlight_menu(2)' onmouseout='unhighlight_menu(2)' class='linkint'>Aktuell-Seite</a></b>.
</div>
<br>
<h3>Wer ist im Vorstand der OL Zimmerberg?</h3>
<div>
Porträts unseres Vorstandes sind auf der <b><a href='?page=6' onmouseover='highlight_menu(6)' onmouseout='unhighlight_menu(6)' class='linkint'>Kontakt-Seite</a></b> zu finden.
</div>
<br>
<h3>Wen kann ich fragen, wenn ich weitere Fragen habe?</h3>
<div style='padding-left:50px;'>";
$buessli_assignees = $buessli_role->getUsers();
foreach ($buessli_assignees as $buessli_assignee) {
    echo olz_user_info_card($buessli_assignee);
}
echo "</div>";

?>
