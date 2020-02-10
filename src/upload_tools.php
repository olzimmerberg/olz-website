<?php

function deobfuscate_upload($obfuscated) {
    $semipos = strpos($obfuscated, ';');
    $iv = intval(substr($obfuscated, 0, $semipos));
    $obfusbase64 = substr($obfuscated, $semipos + 1);
    $obfuscontent = base64_decode(str_replace(" ", "+", $obfusbase64));
    $content = '';
    $current = $iv;
    for ($i = 0; $i < strlen($obfuscontent); $i++) {
        $content = $content.chr(ord($obfuscontent[$i]) ^ (($current >> 8) & 0xFF));
        $current = (($current << 5) - $current) & 0xFFFF;
    }
    return $content;
}
