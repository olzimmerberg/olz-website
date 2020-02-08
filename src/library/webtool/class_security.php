<?php
/**
* class security
*
* Script zum automatischen Überprüfen und ggf. Entfernen gefährlicher 
* Variablenwerte (v. a. Usereingaben)  
*
*  ##############################################################################
*  # class_security.php
*  # Klasse zur Verifizierung von Benutzerangaben und zum Erleichterten Abfangen
*  # möglicher Sicherheitslücken.
*  # Copyright (C) 2004 Daniel de West
*  #
*  # class_security.php ist freie Software; Sie dürfen sie unter den Bedingungen der 
*  # GNU Lesser General Public License, wie von der Free Software Foundation 
*  # veröffentlicht, weiterverteilen und/oder modifizieren; entweder gemäß 
*  # Version 2.1 der Lizenz oder (nach Ihrer Option) jeder späteren Version.
*  #
*  # class_security.php wird in der Hoffnung weiterverbreitet, daß sie nützlich 
*  # sein wird, jedoch OHNE IRGENDEINE GARANTIE, auch ohne die implizierte Garantie 
*  # der MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK. Mehr Details 
*  # finden Sie in der GNU Lesser General Public License.
*  #
*  # Sie sollten eine Kopie der GNU Lesser General Public License zusammen mit 
*  # dieser Bibliothek/diesem Programm erhalten haben; falls nicht, schreiben Sie 
*  # an die Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, 
*  # MA 02111-1307, USA.
*  #
*  # Das Regionale Rechenzentrum Erlangen (http://www.rrze.uni-erlangen.de)
*  # erhebt keinen urheberechtlichen Anspruch auf das von
*  #       Daniel de West
*  # geschriebene Programm.
*  ##############################################################################
*
* @package security
* @author Daniel de West
* @copyright Regionales Rechenzentrum Erlangen (RRZE) Das Skript darf frei kopiert,
* verändert oder modifiziert werden. Dabei muss der Urheberrechts-Hinweis erhalten bleiben.
* Der Autor übernimmt keine Garantie oder Haftung für die Funktion des Skripts und/oder
* mögliche Schäden, die durch seinen Gebrauch entstehen.
* @since Version 1.0
* @version $Revision: 1.15 $
*
$Log: class_security.php,v $
Revision 1.15  2005/09/06 12:04:56  unrz23
- Parameter die Arrays sein müssen erhalten als Standardwert jetzt array() statt ""
- Parameter die Arrays sein müssen, werden ggfs. in ein Array umgewandelt

Revision 1.14  2005/07/14 06:59:56  unrz23
Bugfix: is_boolean in is_bool geändert

Revision 1.13  2005/02/07 12:57:57  unrz23
is_rrze_kennung: Jetzt werden auch Kennungen mit 1 Zahl anerkannt

Revision 1.12  2005/01/14 10:04:47  unrz23
standard: Zeilenumbruch und Tab sind jetzt erlaubt

Revision 1.11  2005/01/11 10:19:34  unrz23
Einfügung der GPL-Lizenz

Revision 1.10  2004/12/20 14:32:24  unrz23
Einbinden der Revisions-Logs in Class-Defintion

Revision 1.9  2004/12/20 09:04:17  unrz23
Auch in der Funktion is_name werden jetzt praktisch alle Umlaute durchgelassen.

Revision 1.7  2004/11/30 15:33:50  unrz23
Doku aktualisiert

Revision 1.6  2004/11/30 15:32:47  unrz23
Übergabe-Parameter $v bei is_url, is_ip und is_uhrzeit eingefügt.

Revision 1.5  2004/11/12 11:06:27  unrz23
Letzte Übertragung war fehlerhaft

Revision 1.4  2004/11/12 10:40:24  unrz23
Doku angepasst

Revision 1.3  2004/11/12 10:37:25  unrz23
Bugfix in check_REQUEST
In standard werden auch ? und ! durchgelassen.

Revision 1.2  2004/09/15 09:24:47  unrz109
Expansion tags dazugefuegt (Id, Log)
*/
class security {

/**
* @var array $std_nicht_ueberpruefen Hier können Standardwerte für $nicht_ueberpruefen festgelegt werden, die immer greifen,
* wenn check() aufgerufen wird.
* Der Aufbau hierfür ist beispielsweise: array("var1","var2");
* @since Version 1.0
*/
var $std_nicht_ueberpruefen=array("fm_action","fm_submit");

/**
* @var array $std_sonderbehandlung Hier können Standardwerte für $sonderbehandlung festgelegt werden, die immer greifen,
* wenn check() aufgerufen wird.
* @since Version 1.0
* Der Aufbau kann entweder
* 1. Ein eindimensionales assoziatives Array sein: z. B. array("var1"=>"zahl","var2"=>"plz","var3"=>"sicheres_passwort")
* 2. Ein mehrdimensionales assoziatives Array sein: z. B. array("var1"=>array("standard","zahl"),"var2"=>array("standard","plz"),"var3"=>array("standard","email"))
* 3. Eine Mischform aus beidem sein: z. B.array("var1"=>"zahl","var2"=>array("standard","plz"),"var3"=>"sicheres_passwort")
*/
var $std_sonderbehandlung=array();

/**
* @var string $current_key In dieser Variable wird der Name des aktuellen Key gespeichert
* @access private
* @since Version 1.0
*/
var $current_key="";

/**
* @var string $current_value In dieser Variable wird der Wert des aktuellen Wertes gespeichert
* @access private
* @since Version 1.0
*/
var $current_value="";

/**
* @var array $error In dieser Variable werden alle aufgetretenen Fehlermeldungen gespeichert
* Dabei werden die Namen der Methode, in denen der Fehler aufgetreten ist, der Name der Variablen und eine evtl. Fehlermeldung vermerkt.
* Beispiel für Wert von error:
* array("0"=>array("method"=>"standard","key"=>"var1","message"=>"Aus 'a%bc' wurde 'abc' gemacht"),
* "1"=>array("method"=>"is_plz","key"=>"var2","message"=>"9478 hat zu wenig Zahlen"))
* @since Version 1.0
*/
var $error=array();

/**
* @var integer $max_length Die maximale Länge, die Variablen haben dürfen
* @access private
* @since Version 1.8
*/
var $max_length=10000;

/**
* function security::set_std_nicht_ueberpruefen
* 
* Mit dieser Funktion kann der Standard-Wert für $nicht_ueberpruefen verändert oder neu gesetzt werden
* 
* @param array $new Array mit den neu hinzuzufügenden Variablennamen, die nicht überprüft werden sollen
* @param boolean $override Gibt an, ob und ggfs. wie bereits vorhandene Variablennamen überschrieben werden sollen: 
* - FALSE Fügt noch nicht vorhandene Variablennamen hinzu
* - TRUE Ersetzt die alten Werte komplett durch die neuen Werte
* @return boolean Gibt TRUE oder FALSE zurück, je nachdem, ob die Aktion erfolgreich war oder nicht
* @since Version 1.0
*/
function set_std_nicht_ueberpruefen($new,$override=FALSE) {
        if (!is_array($new)) return FALSE; // Gibt eine Fehlermeldung zurück, wenn es sich bei $new nicht um ein Array handelt
        if (!is_bool($override)) return FALSE; // Gibt FALSE zurück, wenn es sich bei $override nicht um eine Boolesche Variable handelt
        if ($override===TRUE) $this->std_nicht_ueberpruefen=$new; // Wenn override true oder 1 ist, wird überschrieben
        else $this->std_nicht_ueberpruefen=array_merge($new,$this->std_nicht_ueberpruefen); // andernfalls werden nur nicht bereits vorhandene Keys hinzugefügt
        return TRUE;
}

/**
* function security::set_std_sonderbehandlung
* 
* Mit dieser Funktion kann der Standard-Wert für $sonderbehandlung verändert oder neu gesetzt werden
* 
* @param array $new Array mit den neu hinzuzufügenden Variablennamen und deren Sonderbehandlungsroutine(n), die nicht überprüft werden sollen
* @param boolean $override Gibt an, ob und ggfs. wie bereits vorhandene Variablennamen überschrieben werden sollen: 
* - FALSE Fügt noch nicht vorhandene Variablennamen und Sonderroutinen hinzu
* - TRUE Ersetzt die alten Werte komplett durch die neuen Werte
* - Default-Wert ist TRUE
* @return boolean Gibt TRUE oder FALSE zurück, je nachdem, ob die Aktion erfolgreich war oder nicht
* @since Version 1.0
*/
function set_std_sonderbehandlung($new,$override=TRUE) {
        if (!is_array($new)) return FALSE; // Gibt eine Fehlermeldung zurück, wenn es sich bei $new nicht um ein Array handelt
        if (!is_bool($override)) return FALSE; // Gibt FALSE zurück, wenn es sich bei $override nicht um eine Boolesche Variable handelt
        if ($override===TRUE) $this->std_sonderbehandlung=$new; // Wenn override true oder 1 ist, wird überschrieben
        else $this->std_sonderbehandlung=array_merge($new,$this->std_sonderbehandlung); // andernfalls werden nur nicht bereits vorhandene Keys hinzugefügt
        return TRUE;    
}

/**
* function security::set_current_key
* 
* Mit dieser Funktion wird der Name des aktuellen Keys gesetzt.
* Diese Funktion wird nur intern verwendet
* 
* @param string $new_key Name des neuen Key
* @access private
* @since Version 1.0
*/
function set_current_key($new_key) {
        $this->current_key=$new_key;
}

/**
* function security::set_current_value
* 
* Mit dieser Funktion wird der Wert der aktuellen Variable gesetzt.
* Diese Funktion wird nur intern verwendet
* 
* @param string $new_value Wert der aktuellen Variable
* @access private
* @since Version 1.0
*/
function set_current_value($new_value) {
        $this->current_value=$new_value;
}

/**
* function security::set_max_length
* 
* Mit dieser Funktion wird der Wert für die maximale Variablenlänge gesetzt. Der Wert muss mindestens 1 betragen.
* 
* @param string $new_value Wert für die maximale Variablenlänge
* @access public
* @since Version 1.8
*/
function set_max_length($new_value) {
    if (!$new_value>=1) die ("Konfigurationsfehler: Der Wert für max_length muss mindestens 1 betragen");
    else $this->max_length=$new_value;
}

/**
* function security::add_error
* 
* Mit dieser Funktion wird der Name des aktuellen Keys gesetzt.
* Diese Funktion wird nur intern verwendet
* 
* @param string $method Name der Methode, in der der Fehler aufgetreten ist
* @param string $message Nachricht, die näheres über den Fehler verrät
* @access private
* @since Version 1.0
*/
function add_error($method, $message) {
        $this->error[]=array("method"=>$method,"key"=>$this->current_key,"message"=>$message);
}

/**
* function security::get_error
* 
* Mit dieser Funktion kann auf die bisher angefallenen Fehlermeldungen zugegriffen werden.
* Ohne Parameter werden alle Fehlermeldungen zurückgegeben
* Wird $method angegeben, werden nur Fehlermeldungen, die zu dieser Methode gehören zurückgegeben
* Wird $varname angegeben, werden nur Fehlermeldungen, die zu diesem Variablennamen gehören zurückgegeben
* Wird $varname und $method angegeben, werden nur Fehlermeldungen, die zu dieser Methode und diesem Variablennamen gehören zurückgegeben
* 
* @param string $method Name der Methode, in der der Fehler aufgetreten ist
* @param string $varname Name der Variable, die geprüft wurde
* @return array Es wird ein Array mit den entsprechenden Fehlermeldungen zurückgegeben
* @since Version 1.0
*/
function get_error($method="",$varname="") {
        $output=array();
        // Wenn keine Einschränkungen gemacht werden, werden alle Meldungen exportiert
        if ($method=="" and $varname=="") {
                return $this->error;
        }       
        foreach ($this->error as $nummer=>$error) {
                // Variante 1: Nur Varname definiert
                if ($method=="" and $varname!="" and $error['key']==$varname) $output[$nummer]=$error;
                // Variante 2: Nur Method definiert
                if ($method!="" and $varname=="" and $error['method']==$method) $output[$nummer]=$error;
                // Variante 3: Method und Varname definiert
                if ($method!="" and $error['method']==$method and $varname!="" and $error['key']==$varname) $output[$nummer]=$error;
        }
        return $output;
}

/**
* function security::check
* 
* Überprüft Variablen, ob Sie den erwarteten Werten entsprechen und nimmt
* ggfs. automatisch Veränderungen an den Werten vor.
* Weitere Funktionen für Sonderbehandlungen können natürlich jederzeit
* hinzugefügt werden.
* Achtung: Die Skriptausführung wird mit einer Fehlermeldung beendet, wenn in std_sonderbehandlung oder
* im Parameter $sonderbehandlung ein nicht vorhandener Funktionsname angegeben wird
* 
* @param array $ueberpruefen Assoziatives Array (z. B. $_GET) mit den Namen der Variablen und den dazugehörigen Werten. Aufbau: array("NAME"=>WERT,"name2"=>wert2)
* @param array $nicht_ueberpruefen Array mit Variablennamen, die nicht überprüft werden sollen. Aufbau: array("NAME1","name2","Name3")
* @param array $sonderbehandlung Array mit Variablennamen, die nicht mit (nur) der Standardmethode überprüft werden sollen und Angabe der stattdessen zu verwendenden Methode(n). Aufbau: array("NAME1"=>"zahl","NAME2"=>array("standard","sicheres_passwort"))
* @return array Zurückgegeben wird ein assoziatives Array (wie $ueberpruefen) mit den überprüften und ggfs. veränderten Werten
* @since Version 1.0
*/
function check($ueberpruefen,$nicht_ueberpruefen,$sonderbehandlung) {
        // Ggfs. Parameter in Arrays umwandeln
        if (!is_array($ueberpruefen)) {
            $ueberpruefen=array($ueberpruefen);
        }
        if (!is_array($nicht_ueberpruefen)) {
            $nicht_ueberpruefen=array($nicht_ueberpruefen);
        }
        if (!is_array($sonderbehandlung)) {
            $sonderbehandlung=array($sonderbehandlung);
        }
        // Die übergebenen Werte werden mit den Standardwerten zusammengefügt. Ggfs. werden Standard-Elemente von übergebenen Elementen mit gleichem Namen überschrieben
        $sonderbehandlung=array_merge($this->std_sonderbehandlung,$sonderbehandlung);
        $nicht_ueberpruefen=array_merge($this->std_nicht_ueberpruefen,$nicht_ueberpruefen);
        
        $output=array(); // Initialisieren der Output-Variablen als leeres Array
    // Beginn der eigentlichen Überprüfungen
        foreach ($ueberpruefen as $k=>$v) {
                $this->set_current_key($k); // Zuweisen des aktuellen Keys
                $this->set_current_value($v); // Zuweisen des aktuellen Wertes
        if (!in_array($k,$nicht_ueberpruefen)) { // Nur Variablen, die nicht in $nicht_ueberpruefen stehen, werden behandelt
                        if (!array_key_exists($k,$sonderbehandlung)) $v=$this->standard($v); // Wenn der Variablenname nicht in $sonderbehandlung vorkommt, wird die Standard-Überprüfung verwendet
                        else {
                                // Wenn nur 1 Methode in der Sonderbehandlung angegeben wird:
                                if (!is_array($sonderbehandlung[$k])) {
                                        $function_name="is_".$sonderbehandlung[$k];
                                        if (method_exists($this,$function_name)) $v=$this->$function_name($v);
                                        else die("Konfigurationsfehler: Die Funktion ".$function_name." existiert nicht!");
                                }
                                // Wenn mehrere Methoden in der Sonderbehandlung angegeben werden:
                                else {
                                        foreach ($sonderbehandlung[$k] as $function_name) { // Schleife, die alle in $sonderbehandlung genannten Funktionen nacheinander durcharbeitet
                                                $function_name="is_".$function_name;
                                                if (function_name=="standard") $v=$this->standard($v); // Wenn die Standardmethode verwendet werden soll
                                                else {
                                                        if (method_exists($this,$function_name)) $v=$this->$function_name($v);
                                                        else die("Konfigurationsfehler: Die Funktion ".$function_name." existiert nicht!");
                                                }
                                        }
                                }
                        }
                }                       
                $output[$k]=$v; // Zuweisung von Variablenname und Wert
    }
    return $output;
}

/**
* function security::standard
* 
* Standardmethode zur Überprüfung von Variablen
* Es werden alle Zeichen, die nicht explizit als erlaubt angegeben sind, entfernt.
* Erlaubt sind alle ASCII-Buchstaben, sowie Zeilenumbruch, Tab, Leerzeichen, der Bindestrich, . (Punkt), ?, !, @ und die deutschen Umlaute
* Achtung: Die Methode security::standard ruft automatisch security::is_sql_safe auf!
*
* @author Daniel de West
* @param string $v Wert der Variable
* @return string Rückgabewert ist die um alle nicht explizit erlaubten Zeichen bereinigte Variable
* @since Version 1.0

* uu/16.4.08: In der Standardüberprüfung werden nur is_sql_safe ausgeführt.
*/
function standard($v) {
      //  $v=strip_tags($v); // Entfernt PHP und HTML-Tags        
// ORIGINAL        $v=preg_replace("%[^\w @\.!?\-¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ\n\t]%","",$v); // Entfernen aller nicht explizit gewollten Zeichen
//        $v=preg_replace("%[^\w @\.!?\-¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ\n\t:,/();]%","",$v); // Entfernen aller nicht explizit gewollten Zeichen
        if ($v!=$this->current_value) $this->add_error("standard","'".$this->current_value."' wird zu '".$v."' umgewandelt, da andere als die erlaubten Zeichen enthalten waren.");
        $v=$this->is_sql_safe($v);
        return $v;
}

/**
* function security::is_sql_safe
* 
* Standardmethode zur Entfernung möglicher SQL-Injections
* Alle potentiell für SQL-Injections geeigneten Zeichen werden entfernt. Diese sind:
* - --
* - ;
* - Insert
* - Dump
* - Select
* - Update
* - Delete
* - xp_
* - "
* - '
*
* @author Daniel de West
* @param string $v Wert der Variable
* @return string Rückgabewert ist die um alle potentiellen SQL-Injection-relevanten Zeichen bereinigte Variable
* @since Version 1.0
*/
function is_sql_safe($v) {
//        $search=array("--",";","insert","dump","select","update","delete","xp_",'"',"'"); // Definition der gefährlichen Variablen
        $search=array("--","insert","dump","select","update","delete","xp_"); // Definition der gefährlichen Variablen
        foreach ($search as $s) {
                $v=preg_replace("/".mb_sql_regcase($s)."/","",$v);
        }       
        if ($v!=$this->current_value) $this->add_error("is_sql_safe","'".$this->current_value."' wird zu '".$v."' umgewandelt, da potentiell für SQL-Injections geeignete Zeichen enthalten sind.");
        return $v;
}

/**
* function security::is_zahl
* 
* Überprüft Variablen, ob Sie nur aus Zahlen bestehen.
* Falls ja wird der Wert der Zahl, falls nein wird FALSE zurückgegeben.
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Zahl werden automatisch entfernt.
* 
* @author Daniel de West
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder eine Zahl oder FALSE
* @since Version 1.0
*/
function is_zahl($v) {
        $v=trim($v);
        if (!is_numeric($v)) {  
                $this->add_error("is_zahl","'".$this->current_value."' ist keine Zahl.");
                return FALSE; // Löschen des Variablenwertes, wenn es sich nicht um eine Zahl handelt           
        }
        return $v;
}

/**
* function security::is_sicheres_passwort
* 
* Überprüft Variablen, ob Sie sich als sicheres Passwort eignen.
* Falls ja wird das Passwort, falls nein wird FALSE zurückgegeben.
* Überprüft wird, ob das vorgeschlagene Passwort:
* - mindestens 6 Zeichen lang ist
* - mindestens 2 Sonderzeichen enthält
* - mindestens 2 Zahlen enthält
* - mindestens 2 Buchstaben enthält
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Zahl werden automatisch entfernt.
* 
* @author Daniel de West
* @param string $v Vorgeschlagenes Passwort
* @return mixed Rückgabewert ist entweder das unveränderte sichere Passwort oder FALSE
* @since Version 1.0
*/
function is_sicheres_passwort($v) {
        $v=trim($v);
        if (strlen($v)<6) $v=FALSE; // Minimale Länge ist 6
        if (!preg_match('%^.*[^a-zA-Z0-9]+.*[^a-zA-Z0-9]+.*$%',$v)) {
                $v=FALSE; // Mindestens 2 Sonderzeichen
                $this->add_error("is_sicheres_passwort","'".$this->current_value."' enthält nicht mindestens 2 Sonderzeichen.");
        }
        else {
                if (!preg_match('%^.*[0-9]+.*[0-9]+.*$%',$v)) {
                        $v=FALSE; // Mindestens 2 Zahlen
                        $this->add_error("is_sicheres_passwort","'".$this->current_value."' enthält nicht mindestens 2 Zahlen.");
                }
                else {
                        if (!preg_match('%^.*[a-zA-Z]+.*[a-zA-Z]+.*$%',$v)) {
                                $v=FALSE; // Mindestens 2 Buchstaben
                                $this->add_error("is_sicheres_passwort","'".$this->current_value."' enthält nicht mindestens 2 Buchstaben.");
                        }
                }
        }       
        return $v;
}

/**
* function security::is_email
* 
* Überprüft Variablen, ob Sie gültige E-Mail-Adressen sind.
* Falls ja wird die Mailadresse, falls nein wird FALSE zurückgegeben.
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Mailadresse werden automatisch entfernt.
* 
* @author Christian Kruse (Entnommen von www.selfhtml.teamone.de)
* @param string $v Vorgeschlagene Mailadresse
* @return mixed Rückgabewert ist entweder die unveränderte Mailadresse oder FALSE
* @since Version 1.0
*/
function is_email($v) {
        $v=trim($v);
        $nonascii      = "\x80-\xff"; # Non-ASCII-Chars are not allowed

        $nqtext        = "[^\\\\$nonascii\015\012\"]";
        $qchar         = "\\\\[^$nonascii]";
        
        $protocol      = '(?:mailto:)';
        $normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
        $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
        $user_part     = "(?:$normuser|$quotedstring)";

        $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
        $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
        $dom_tldpart   = '[a-zA-Z]{2,5}';
        $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";

        $regex         = "$protocol?$user_part\@$domain_part";
        if (preg_match("/^$regex$/",$v)) return $v;
        else {
                $this->add_error("is_email","'".$this->current_value."' ist keine gültige E-Mail-Adresse.");
                return FALSE;
        }
}

/**
* function security::is_plz
* 
* Überprüft Variablen, ob Sie mögliche Postleitzahlen sind.
* Falls ja wird der Wert der PLZ, falls nein wird FALSE zurückgegeben.
* Die PLZ können mit vorangestelltem Länderkürzel oder ohne selbiges sein (z. B. D-90408)
* Die Zahl selber muss mindestens 5, maximal 8 Ziffern haben (wg. ausländischer PLZ)
* Bei PLZ, die mit D- anfangen dürfen am Ende nur 5 Ziffern stehen
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Zahl werden automatisch entfernt.
* 
* @author Daniel de West
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder eine gültige PLZ oder FALSE
* @since Version 1.0
*/
function is_plz($v) {
        $v=trim($v);
        if (!preg_match("/^([a-zA-Z]+-)?\d{5,8}$/",$v)) {
                $v=FALSE; // Gibt FALSE zurück, wenn es sich nicht um eine gültige PLZ handelt
                $this->add_error("is_plz","'".$this->current_value."' ist keine gültige Postleitzahl.");
        }
        else {
                if (substr($v,0,2)=="D-" and !preg_match("/^D-\d{5}$/",$v)) {
                        $v=FALSE;// Sonderbehandlung für deutsche PLZ, hier wird auf genau 5 Ziffern überprüft
                        $this->add_error("is_plz","'".$this->current_value."' fängt mit D- an, hat dann aber nicht (nur) die geforderten 5 Ziffern.");
                }
        }
        return $v;
}

/**
* function security::is_datum
* 
* Überprüft Variablen, ob Sie ein gültiges Datum im Format TT.MM.JJJJ sind.
* Überprüft wird neben der äußeren Form auch, ob der Monat tatsächlich die angegebene Anzahl von Tagen
* hat, wobei Schaltjahre entsprechende berücksichtigt werden.
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach dem Datum werden automatisch entfernt.
* 
* @author Entnommen aus einem Artikel der Zeitschrift Internet World (www.internetworld.de), Ausgabe 8/2004
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder ein Datum im Format TT.MM.JJJJ oder FALSE
* @since Version 1.0
*/
function is_datum($v) {
        $v=trim($v);
        $expr = "/ (
                        (                               # NORMALES DATUM - KEIN SCHALTJAHR
                        ((0[1-9]|[12][\d])|30)  # Tag im Bereich 01 bis 30 ?
                        \.                              # -=Trennzeichen=-
                        (0[13-9]|1[0-2])                # und Monat mit 30 Tagen ?
                        )|(                             # oder
                        31                              # Tag gleich 31 ?
                        \.                              # -=Trennzeichen=-
                        (0[13578]|1[02])                # und Monat mit 31 Tagen ?
                        )|(                             # oder
                        (0[1-9]|[12][0-8])              # Tag im Bereich 01 bis 28 ?
                        \.                              # -=Trennzeichen=-
                        02                              # und Monat gleich 02 (Februar) ?
                        )
                        \.                              # -=Trennzeichen=-
                        [1-9]\d\d\d                     # und Jahreszahl >= 1000 ?
                        )|(                             # 29. FEBRUAR IM SCHALTJAHR
                        29                              # Tag gleich 29
                        \.                              # -=Trennzeichen=-
                        02                              # Monat gleich 02 (Februar)
                        \.                              # -=Trennzeichen=-
                        (                               # JAHR DURCH 4 ABER NICHT DURCH 100 TEILBAR?
                        [1-9]\d                 # Den zwei ersten Ziffern der Jahreszahl
                        (
                        [2468][048]             # folgt eine gerade Zahl und dann eine 0, 4 oder 8
                        |                               # oder
                        0[48]                   # es folgt eine Null und dann eine 4 oder 8
                        |                               # oder
                        [13579][26]             # es folgt eine ungerade Zahl und dann eine 2 oder 6
                        )
                        )|(                             # ODER JAHR DURCH 400 TEILBAR?
                        ([2468][048]            # Jahreszahl beginnt mit gerader Zahl und es folgt eine 0, 4 oder 8
                        |                               # oder
                        [13579][26]             # Jahreszahl beginnt mit ungerader Zahl und es folgt eine 2 oder 6
                        )                
                        00                              # Jahreszahl endet auf 00 und ist damit durch 100 teilbar
                        )
                        )
                        /x";
        if (!preg_match($expr,$v)) {
                $v=FALSE; // Löschen des Variablenwertes, wenn es sich nicht um ein gültiges Datum handelt
                $this->add_error("is_datum","'".$this->current_value."' ist kein Datum im Format TT.MM.JJJJ oder enthält ungültige Werte.");
        }
        return $v;
}

/**
* function security::is_url
* 
* Überprüft Variablen, ob Sie eine syntaktisch korrekte URI sind.
* Als gültig erkannt werden die Protokolle http,ftp,https
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Web-Adresse werden automatisch entfernt.
* 
* @author Entnommen aus einem Artikel der Zeitschrift Internet World (www.internetworld.de), Ausgabe 8/2004
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder ein syntaktisch korrekter URI oder FALSE
* @since Version 1.0
*/
function is_url($v) {
        $expr="/^(((ht|f)tp(s?))\:\/\/)([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+&%\$#\=~_\-]+))*$/";
        if (!preg_match($expr,$v)) {
                $v=FALSE; // Löschen des Variablenwertes, wenn es sich nicht um eine gültige web-Adresse handelt
                $this->add_error("is_url","'".$this->current_value."' ist keine gültige URL.");
        }
        return $v;      
}

/**
* function security::is_ip
* 
* Überprüft Variablen, ob Sie eine syntaktisch korrekte IP-Adresse sind.
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der IP-Adresse werden automatisch entfernt.
* 
* @author Entnommen aus einem Artikel der Zeitschrift Internet World (www.internetworld.de), Ausgabe 8/2004
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder eine syntaktisch korrekte IP-Adresse oder FALSE
* @since Version 1.0
*/
function is_ip($v) {
        $expr="/^(((1?\d?\d)|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}/";
        if (!preg_match($expr,$v)) {
                $v=FALSE; // Löschen des Variablenwertes, wenn es sich nicht um eine gültige IP-Adresse handelt
                $this->add_error("is_ip","'".$this->current_value."' ist keine gültige IP-Adresse.");
        }
        return $v;      
}

/**
* function is_uhrzeit
* 
* Überprüft Variablen, ob Sie eine Uhrzeitangabe im Format SS:MM sind.
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach der Uhrzeit werden automatisch entfernt.
* 
* @author Entnommen aus einem Artikel der Zeitschrift Internet World (www.internetworld.de), Ausgabe 8/2004
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder eine Uhrzeitangabe im Format SS:MM oder FALSE
* @since Version 1.0
*/
function is_uhrzeit($v) {
        $expr="/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/";
        if (!preg_match($expr,$v)) {
                $v=FALSE; // Löschen des Variablenwertes, wenn es sich nicht um eine Uhrzeitangabe im Format SS:MM handelt
                $this->add_error("is_uhrzeit","'".$this->current_value."' ist keine gültige Uhrzeitangabe im Format SS:MM.");
        }
        return $v;      
}

/**
* function security::is_name
* 
* Überprüft Variablen, ob Sie ein Personenname sein können.
* Zugelassen werden 1-5 durch Leerzeichen getrennte Textblöcke, in denen außer Buchstaben nur das Zeichen - (Bindestrich) vorkommen darf
* Achtung: Eventuelle Leerräume (Whitespaces) vor und nach dem Namen werden automatisch entfernt.
* 
* @author Daniel de West
* @param string $v Wert der Variable
* @return mixed Rückgabewert ist entweder ein möglicher Personenname oder FALSE
* @since Version 1.0
*/
function is_name($v) {
        $expr="/^([a-zA-Z¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ\-\.]+ {0,1}){1,5}$/";
        if (!preg_match($expr,$v)) {
                $v=FALSE; // Löschen des Variablenwertes, wenn es sich nicht um einen Namen handeln kann
                $this->add_error("is_name","'".$this->current_value."' ist keine gültige Namensangabe. Häufig sind unerlaubte Sonderzeichen ein Grund dafür.");
        }
        return $v;      
}

/**
* function security::is_filename
* 
* Überprüft, ob ein Wert ein Dateiname sein kann
* Erlaubt sind nur Buchstaben (ohne Umlaute), der Bindestrich, der Unterstrich und Zahlen
* Es darf nur 1 Punkt vorkommen, nach dem eine 2-6stellige Dateiendung kommen muss, die nur Zahlen oder Buchstaben enthalten darf.
*
* @author Daniel de West
* @param string $v Wert der Variable
* @return string Rückgabewert ist entweder ein möglicher Dateiname oder FALSE
* @since Version 1.0
*/
function is_filename($v) {
        if (!preg_match("%^[\w0-9\-\_]+\.[a-zA-Z0-9]{2,6}$%",$v)) {
                $this->add_error("is_filename","'".$this->current_value."' kann kein filename sein.");
                return FALSE;
        }
        else return $v;
}

/**
* function security::is_kundennummer
* 
* Überprüft, ob ein Wert eine gültige RRZE-Kundennummer ist
* RRZE-Kundennummern haben am Beginn 2 Buchstaben, gefolgt von 2 Zeichen, die Buchstaben oder Ziffern sein können und danach 3 Ziffern
* Achtung: Leerzeichen am Beginn und Ende werden automatisch entfernt
*
* @author Daniel de West
* @param string $v Wert der Variable
* @return string Rückgabewert ist entweder eine gültige Kundennummer oder FALSE
* @since Version 1.0
*/
function is_kundennummer($v) {
        $v=trim($v);
        if (!preg_match("%^[a-zA-Z]{2}[a-zA-Z0-9]{2}[0-9]{3}$%",$v)) {
                $this->add_error("is_kundennummer","'".$this->current_value."' kann keine Kundennummer sein.");
                return FALSE;
        }
        else return $v;
}

/**
* function security::is_rrze_kennung
* 
* Überprüft, ob ein Wert eine gültige RRZE-Kennung ist
* RRZE-Kennungen haben am Beginn "unrz",dann 1 oder keinen Buchstaben gefolgt von 1, 2 oder 3 Ziffern
* Es wird nicht überprüft, ob die Kennung existiert, sondern nur, ob Sie syntaktisch korrekt ist.
* Achtung: Leerzeichen am Beginn und Ende werden automatisch entfernt und es wird in Kleinbuchstaben umgewandelt
*
* @author Daniel de West
* @param string $v Wert der Variable
* @return string Rückgabewert ist entweder eine gültige RRZE-Kennung oder FALSE
* @since Version 1.0
*/
function is_rrze_kennung($v) {
        $v=trim($v);
        $v=strtolower($v);
        if (!preg_match("%^unrz[a-zA-Z]?[0-9]{1,3}$%",$v)) {
                $this->add_error("is_rrze_kennung","'".$this->current_value."' kann keine RRZE-Kennung sein.");
                return FALSE;
        }
        else return $v;
}

/**
* function security::check_POST
* 
* Überprüft alle Variablen in $_POST und deren Entsprechung in $_REQUEST.
* Es werden keine Parameter erwartet, es können aber die unten genannten übergeben werden.
* Es werden keine Parameter zurückgegeben, da die Änderungen direkt in $_POST
* und in $_REQUEST vorgenommen werden.
* 
* @param array $nicht_ueberpruefen Es kann ein vom in std_nicht_ueberpruefen definierten abweichender oder ergänzender Wert übergeben werden.
* @param array $sonderbehandlung Es kann ein vom in stad_sonderbehandlung definierten abweichender oder ergänzender Wert übergeben werden.
* @since Version 1.0
*/
function check_POST($nicht_ueberpruefen=array(),$sonderbehandlung=array()) {
        // Ggfs. Parameter in Arrays umwandeln
        if (!is_array($nicht_ueberpruefen)) {
            $nicht_ueberpruefen=array($nicht_ueberpruefen);
        }
        if (!is_array($sonderbehandlung)) {
            $sonderbehandlung=array($sonderbehandlung);
        }
        $_POST=$this->check($_POST,$nicht_ueberpruefen,$sonderbehandlung);
        foreach ($_POST as $k=>$v) {
                $erg=$this->check(array($k=>$v),$nicht_ueberpruefen,$sonderbehandlung);
                $_REQUEST[$k]=$erg[$k];
        }
}

/**
* function security::check_GET
* 
* Überprüft alle Variablen in $_GET und deren Entsprechung in $_REQUEST.
* Es werden keine Parameter erwartet, es können aber die unten genannten übergeben werden.
* Es werden keine Parameter zurückgegeben, da die Änderungen direkt in $_GET
* und in $_REQUEST vorgenommen werden.
* 
* @param array $nicht_ueberpruefen Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @param array $sonderbehandlung Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @since Version 1.0
*/
function check_GET($nicht_ueberpruefen=array(),$sonderbehandlung=array()) {
        // Ggfs. Parameter in Arrays umwandeln
        if (!is_array($nicht_ueberpruefen)) {
            $nicht_ueberpruefen=array($nicht_ueberpruefen);
        }
        if (!is_array($sonderbehandlung)) {
            $sonderbehandlung=array($sonderbehandlung);
        }
        $_GET=$this->check($_GET,$nicht_ueberpruefen,$sonderbehandlung);
        foreach ($_GET as $k=>$v) {
                $erg=$this->check(array($k=>$v),$nicht_ueberpruefen,$sonderbehandlung);
                $_REQUEST[$k]=$erg[$k];         
        }
}

/**
* function security::check_REQUEST
* 
* Überprüft alle Variablen in $_REQUEST und deren Entsprechung in $_GET und $_POST.
* Es werden keine Parameter erwartet, es können aber die unten genannten übergeben werden.
* Es werden keine Parameter zurückgegeben, da die Änderungen direkt in $_POST, $_GET
* und in $_REQUEST vorgenommen werden.
* 
* @param array $nicht_ueberpruefen Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @param array $sonderbehandlung Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @since Version 1.0
*/
function check_REQUEST($nicht_ueberpruefen=array(),$sonderbehandlung=array()) {
        // Ggfs. Parameter in Arrays umwandeln
        if (!is_array($nicht_ueberpruefen)) {
            $nicht_ueberpruefen=array($nicht_ueberpruefen);
        }
        if (!is_array($sonderbehandlung)) {
            $sonderbehandlung=array($sonderbehandlung);
        }
        $_REQUEST=$this->check($_REQUEST,$nicht_ueberpruefen,$sonderbehandlung);
        foreach ($_REQUEST as $k=>$v) {
                if (isset($_POST[$k])) {
                    $_POST[$k]=$v;
                }
                if (isset($_GET[$k])) {
                    $_GET[$k]=$v;
                }
        }
}

/**
* function security::check_SESSION
* 
* Überprüft alle Variablen in $_SESSION
* Es werden keine Parameter erwartet, es können aber die unten genannten übergeben werden.
* Es werden keine Parameter zurückgegeben, da die Änderungen direkt in $_SESSION vorgenommen werden.
* 
* @param array $nicht_ueberpruefen Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @param array $sonderbehandlung Es kann ein vom in standard() definierten abweichender oder ergänzender Wert übergeben werden.
* @since Version 1.0
*/
function check_SESSION($nicht_ueberpruefen=array(),$sonderbehandlung=array()) {
        // Ggfs. Parameter in Arrays umwandeln
        if (!is_array($nicht_ueberpruefen)) {
            $nicht_ueberpruefen=array($nicht_ueberpruefen);
        }
        if (!is_array($sonderbehandlung)) {
            $sonderbehandlung=array($sonderbehandlung);
        }
        $_SESSION=$this->check($_SESSION,$nicht_ueberpruefen,$sonderbehandlung);
}

/**
* function security::check_UPLOADS
* 
* Überprüft alle Variablen in $_FILES (und deren Entsprechungen in $HTTP_POST_FILES)
* ob es sich um echte Dateinamen handeln kann. Falls nicht werden sie gelöscht.
* Es können keine Parameter übergeben werden
* @since Version 1.0
*/
function check_UPLOADS() {
        print_r($_FILES);
        foreach ($_FILES as $uploadname=>$f) {
                echo "Überprüfe ".$f['name'].".<br>"; 
                if ($this->is_filename($f['name'])===FALSE) { // Überprüfung, ob der Dateiname ein echter Dateiname sein kann
                        echo "jetzt geht es ".$f['name']." an den Kragen.<br>"; 
                        // Wenn es kein echter Dateiname sein kann: 
                        unlink ($f['tmp_name']); // Löschen der temporären Datein
                        unset($_FILES[$uploadname]); // Löschen in $_FILES
                        print_r($HTTP_POST_FILES);
                        if ($HTTP_POST_FILES[$uploadname][$f['name']]) {
                                echo "und zwar auch in HTTP_POST_FILES.<br>";
                                if (file_exists($HTTP_POST_FILES[$uploadname][$f['tmp_name']])) unlink ($HTTP_POST_FILES[$uploadname][$f['tmp_name']]); // Löschen der temporären Datei, falls vorhanden
                                unset($HTTP_POST_FILES[$uploadname]); // Löschen in $HTTP_POST_FILES, falls dort vorhanden
                        }
                }
        }
}

} // Ende der Class

function mb_sql_regcase($string,$encoding='auto'){
  $max=mb_strlen($item,$encoding);
  for ($i = 0; $i < $max; $i++) {
    $char=mb_substr($item,$i,1,$encoding);
    $up=mb_strtoupper ($char,$encoding);
    $low=mb_strtolower($char,$encoding);
    $ret.=($up!=$low)?'['.$up.$low.']' : $char;
  }
  return $ret;
}
?>
