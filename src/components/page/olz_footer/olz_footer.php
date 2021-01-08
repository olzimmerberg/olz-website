<?php

echo "<div style='clear:both;'>&nbsp;</div>";
echo "</div>"; // site-background

echo "<div class='footer'>";
echo "<a href='datenschutz.php'>Datenschutz</a>";
echo "</div>"; // footer

echo "</div>"; // site-container

include __DIR__.'/../../auth/olz_login_modal/olz_login_modal.php';
include __DIR__.'/../../auth/olz_sign_up_modal/olz_sign_up_modal.php';
include __DIR__."/../../auth/olz_change_password_modal/olz_change_password_modal.php";

echo "</body>
</html>";
