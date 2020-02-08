<h2>Login</h2>
<table>
    <tr>
        <td style="width:20%;padding-top:4px;font-weight:bold;">Benutzername:</td>
        <td style="width:80%">
            <input type="text" name="username" style="width:95%;" id="focusonload">
        </td>
    </tr>
    <tr>
        <td style="width:20%;padding-top:4px;font-weight:bold;">Passwort:</td>
        <td style="width:80%">
            <input type="password" name="passwort" style="width:95%;">
        </td>
    </tr>
</table>
<script type="text/javascript">
document.getElementById("focusonload").focus();
</script>

<?php
srand ( (double)microtime () * 1000000 );
$_SESSION["challenge"] = rand(100000,999999) ;
echo "<input type='hidden' name='challenge' value='".$_SESSION["challenge"]."'>";

if ($_SESSION["versuch"]==2) $alert = "Falsches Passwort! Du hast noch 1 Versuch zur Verfügung!";
elseif ($_SESSION["versuch"]==1) $alert = "Falsches Passwort! Du hast noch 2 Versuche zur Verfügung!";
elseif ($_SESSION["versuch"]>= $maxversuche) $alert = "Sorry, keine weiteren Versuche!";
if ($alert > "" ) echo "<div class='buttonbar error'>".$alert."</div>";
echo "<div class='buttonbar'>".olz_buttons("button",array("Login"),"")."</div>";
?>
