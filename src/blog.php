<?php

echo "
<div id='content_rechts'>
<form name='Formularr' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>
<div>";
include __DIR__.'/blog_r.php';
echo "</div>
</form>
</div>
<div id='content_mitte'>
<form name='Formularl' method='post' action='index.php#id_edit".$_SESSION['id_edit']."' enctype='multipart/form-data'>";
include __DIR__.'/blog_l.php';
echo "</form>
</div>
";
