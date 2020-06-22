<?php

echo "<div id='content_double'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/library/phpWebFileManager/start.php';
echo "</div>
</form>
</div>";
