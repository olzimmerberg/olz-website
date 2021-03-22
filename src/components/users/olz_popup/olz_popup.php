<?php

function olz_popup($trigger, $popup, $trigger_type = 'click') {
    $ident = md5(rand().rand().microtime(true));
    $ident_for_js = htmlentities(json_encode($ident));
    $out = "<div class='olz-popup'>";
    $out .= "<div class='popup' id='popup{$ident}'>{$popup}</div>";
    $triggers = "";
    if ($trigger_type == 'click') {
        $triggers = "onclick='olzPopupToggle({$ident_for_js})'";
    }
    $out .= "<div {$triggers} class='trigger' id='trigger{$ident}'>{$trigger}</div>";
    $out .= "</div>";
    return $out;
}
