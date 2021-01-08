<?php

require_once __DIR__.'/../../../config/paths.php';
require_once __DIR__.'/../../../config/doctrine_db.php';
require_once __DIR__.'/../../../model/index.php';

$user_repo = $entityManager->getRepository(User::class);
$username = $_SESSION['user'];
$user = $user_repo->findOneBy(['username' => $username]);
$image_path = "{$code_href}icns/user.svg";
if ($user) {
    $user_image_path = "img/users/{$user->getId()}.jpg";
    if (is_file("{$data_path}{$user_image_path}")) {
        $image_path = "{$data_href}{$user_image_path}";
    }
}

echo "<a href='#' role='button' id='account-menu-link' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
echo "<img src='{$image_path}' class='account-thumbnail' />";
echo "</a>";
echo "<div class='dropdown-menu dropdown-menu-right' aria-labelledby='account-menu-link'>";
if ($user) {
    echo "<a class='dropdown-item' href='profil.php'>Profil</a>";
    if (in_array('ftp', preg_split("/ /", $_SESSION['auth'])) || ($_SESSION['auth'] == 'all')) {
        echo "<a class='dropdown-item' href='webftp.php'>WebFTP</a>";
    }
    if ($_SESSION['auth'] == 'all') {
        echo "<a class='dropdown-item' href='results.php'>Online-Resultate</a>";
    }
    echo <<<'ZZZZZZZZZZ'
<a
    id='logout-menu-item'
    class='dropdown-item'
    href='#'
    onclick='olzAccountMenuLogout()'
>
    Logout
</a>
ZZZZZZZZZZ;
} else {
    echo <<<'ZZZZZZZZZZ'
<a
    id='login-menu-item'
    class='dropdown-item'
    href='#'
    role='button'
    data-toggle='modal'
    data-target='#login-modal'
>
    Login
</a>
<a
    id='sign-up-menu-item'
    class='dropdown-item feature sign-up'
    href='#'
    role='button'
    data-toggle='modal'
    data-target='#sign-up-modal'
>
    Konto erstellen
</a>
ZZZZZZZZZZ;
}
echo "</div>";
