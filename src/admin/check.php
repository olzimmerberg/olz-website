<?php
$maxversuche = 3;

// Nutzeridenifikations-Funktion
function check_nutzer()
{global $db, $button, $page,$maxversuche;
if ($page == "Logout") // Logout
    {if (!isset($_SESSION['edit']))
        {$page = 1;
        session_destroy();
        session_unset();
        unset($_SESSION);
        session_start();
        $_SESSION["page"] = $page;
        header('Authorization: Basic '.base64_encode("web276".":"."greotvaar"));
        return false;}
    else
        {$alert = "Bearbeitung muss zuerst abgeschlossen werden.";
        return false;}
    }
if (isset($_SESSION["versuch"]) && $_SESSION["versuch"]>$maxversuche) // Zu viele Versuche
    {$page = $_SESSION["page"];
    return false;}
if (!isset($_SESSION["auth"]) && !isset($_POST["username"])) // Login ohne Username
    {return false;}
if (isset($_POST["username"])) // Login überprüfen
    {if (!isset($_SESSION["versuch"])) // Versuche zählen
        {$_SESSION["versuch"]=1;}
    else
        {$_SESSION["versuch"]++;}
    if (!isset($_COOKIE[session_name()]))
        {return false;}
        $nutzer = trim($_POST["username"]);
        $pwd  = trim($_POST["passwort"]);
        $challenge  = trim($_POST["challenge"]);
        $sql = "SELECT * FROM user WHERE (benutzername = '$nutzer')";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        // if ($pwd == md5($row['passwort'].$_SESSION["challenge"]))
        if ($pwd == $row['passwort'] && $challenge == $_SESSION["challenge"])
            {$_SESSION["auth"]=$row['zugriff']; // Eingaben korrekt
            $_SESSION["root"] = $row["root"];
            if ($_SESSION["root"]=="") $_SESSION["root"] = "OLZimmerbergAblage";
            // Mögliche Werte für 'zugriff': all, ftp, termine, mail
            $page = $_SESSION["page"];
            $_SESSION['user'] = $nutzer;
            //unset($_SESSION["challenge"]);
            return true;}
        else
            {$page = "10"; // Eingaben falsch
            return false;}
    }
}
?>
