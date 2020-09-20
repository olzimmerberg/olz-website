<?php

// =============================================================================
// Das Organigramm unseres Vereins.
// =============================================================================

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/database.php';

echo "<script type='text/javascript'>
var highlighttimer = false;
function highlight_organigramm(id) {
    highlight_organigramm_scroll(id);
}
function highlight_organigramm_scroll(id) {
    var elem = document.getElementById(id);
    if (/box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement.id)) elem = elem.parentElement;
    elem.style.backgroundColor = \"rgba(0,0,0,0)\";
    var rect = elem.getBoundingClientRect();
    var optimalPageYOffset = window.pageYOffset+rect.top+rect.height/2-window.innerHeight/2;
    var nextPageYOffset = window.pageYOffset+(optimalPageYOffset-window.pageYOffset)/4;
    if (nextPageYOffset<=0) {
        window.scrollTo(0, 0);
        highlight_organigramm_color(id)
    } else if (document.getElementsByTagName(\"body\")[0].offsetHeight-window.innerHeight<=nextPageYOffset) {
        window.scrollTo(0, document.getElementsByTagName(\"body\")[0].offsetHeight-window.innerHeight);
        highlight_organigramm_color(id)
    } else if (Math.abs(nextPageYOffset-optimalPageYOffset)<=3) {
        window.scrollTo(0, optimalPageYOffset);
        highlight_organigramm_color(id)
    } else {
        window.scrollTo(0, Math.round(nextPageYOffset));
        window.setTimeout(function () {highlight_organigramm_scroll(id);}, 50);
    }
}
function highlight_organigramm_color(id) {
    var elem = document.getElementById(id);
    if (/box\\-[0-9]+\\-[0-9]+/.exec(elem.parentElement.id)) elem = elem.parentElement;
    for (var i=0; i<20; i++) {
        window.setTimeout((function (i) {return function () {
            elem.style.backgroundColor = \"rgba(0,220,0,\"+Math.pow(Math.sin(i*Math.PI/12), 2)+\")\";
        };})(i), i*100);
    }
}
</script>";

require_once __DIR__.'/model/Role.php';
require_once __DIR__.'/model/RoleRepository.php';
require_once __DIR__.'/model/User.php';
require_once __DIR__.'/model/UserRepository.php';
require_once __DIR__.'/components/users/olz_user_info_with_popup/olz_user_info_with_popup.php';

$role_repo = $entityManager->getRepository(Role::class);
$user_repo = $entityManager->getRepository(User::class);

$colwid = 111;
$root_roles = $role_repo->getRolesWithParent(null);
$org = "<div style='width:100%; overflow-x:scroll;'><table style='table-layout:fixed; width:".($colwid * count($root_roles))."px;'>";
foreach ($root_roles as $root_role) {
    $root_role_name = nl2br($root_role->getName());
    $org .= "<td style='width:".$colwid."px; vertical-align:top;'>";
    $org .= "<div id='link-role-{$root_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
    $org .= "<h6 style='font-weight:bold; min-height:36px;'>{$root_role_name}</h6>";
    $root_role_assignees = $root_role->getUsers();
    foreach ($root_role_assignees as $root_role_assignee) {
        $org .= olz_user_info_with_popup($root_role_assignee, 'name_picture');
    }
    $org .= "</div>";
    $charge_roles = $role_repo->getRolesWithParent($root_role->getId());
    foreach ($charge_roles as $charge_role) {
        $charge_role_name = nl2br($charge_role->getName());
        $org .= "<div style='text-align:center; height:20px; overflow:hidden;'><span style='border-left:1px solid #000000; font-size:20px;'></span></div>";
        $org .= "<div id='link-role-{$charge_role->getId()}' style='margin:0px 0px 0px 1px; padding:0px; border:1px solid #000000; text-align:center;'>";
        $org .= "<h6>{$charge_role_name}</h6>";
        $charge_role_assignees = $charge_role->getUsers();
        foreach ($charge_role_assignees as $charge_role_assignee) {
            $org .= olz_user_info_with_popup($charge_role_assignee, 'name');
        }
        $subcharge_roles = $role_repo->getRolesWithParent($charge_role->getId());
        foreach ($subcharge_roles as $subcharge_role) {
            $subcharge_role_name = nl2br($subcharge_role->getName());
            $org .= "<div id='link-role-{$subcharge_role->getId()}' style='text-align:center; font-style:italic;'>{$subcharge_role_name}</div>";
            $subcharge_role_assignees = $subcharge_role->getUsers();
            foreach ($subcharge_role_assignees as $subcharge_role_assignee) {
                $org .= olz_user_info_with_popup($subcharge_role_assignee, 'name');
            }
        }
        $org .= "</div>";
    }
    $org .= "</td>";
}
$org .= "</table></div>";

echo "<div id='organigramm'><h2>Häufig gesucht</h2>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-5&quot;)' class='linkint'>Präsident</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-6&quot;)' class='linkint'>Mitgliederverwaltung</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-18&quot;)' class='linkint'>Kartenverkauf</a></b></div>
<div><b><a href='javascript:highlight_organigramm(&quot;link-role-19&quot;)' class='linkint'>Kleiderverkauf</a></b></div>
<div><b>PC-Konto: 85-256448-8</b></div>
<h2>Organigramm OL Zimmerberg</h2>".$org."</div>";

echo "<script type='text/javascript'>
function olz_marquee(elem) {
    var om = elem.getAttribute(\"olzmarquee\");
    if (om) {
        var subdiv = document.createElement(\"div\");
        var subspan = document.createElement(\"span\");
        subspan.innerHTML = elem.innerHTML;
        elem.innerHTML = \"\";
        elem.appendChild(subdiv);
        subdiv.style.textAlign = \"inherit\";
        subdiv.style.width = subdiv.offsetWidth+\"px\";
        subdiv.style.overflowX = \"hidden\";
        subdiv.style.whiteSpace = \"nowrap\";
        subdiv.appendChild(subspan);
        if (subdiv.offsetWidth<subspan.offsetWidth) {
            var sw = subspan.offsetWidth-subdiv.offsetWidth;
            window.setTimeout((function (subdiv, sw) {return function () {
                if (subdiv.scrollLeft<sw) {
                    subdiv.scrollLeft += 1;
                    window.setTimeout(arguments.callee, 75);
                } else {
                    window.setTimeout((function (subdiv, cl) {return function () {
                        subdiv.scrollLeft = 0;
                        window.setTimeout(cl, 1500);
                    };})(subdiv, arguments.callee), 1500);
                }
            };})(subdiv, sw), 100);
        }
    }
    var cld = elem.children;
    for (var i=0; i<cld.length; i++) {
        olz_marquee(cld[i]);
    }
}
//olz_marquee(document.getElementById(\"organigramm\"));
</script>";
