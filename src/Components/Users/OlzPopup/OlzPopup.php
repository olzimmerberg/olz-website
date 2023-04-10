<?php

namespace Olz\Components\Users\OlzPopup;

use Olz\Components\Common\OlzComponent;

class OlzPopup extends OlzComponent {
    public function getHtml($args = []): string {
        $trigger = $args['trigger'];
        $popup = $args['popup'];
        $trigger_type = $args['trigger_type'] ?? 'click';

        $ident = md5(rand().rand().microtime(true));
        $ident_for_js = htmlentities(json_encode($ident));
        $out = "<span class='olz-popup'>";
        $out .= "<div class='popup' id='popup{$ident}'>{$popup}</div>";
        $triggers = "";
        if ($trigger_type == 'click') {
            $triggers = "onclick='olz.olzPopupToggle({$ident_for_js})'";
        }
        $out .= "<span {$triggers} class='trigger' id='trigger{$ident}'>{$trigger}</span>";
        $out .= "</span>";
        return $out;
    }
}
