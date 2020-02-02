<?php

require_once("admin/olz_init.php");

$result = $db->query("SELECT v FROM facebook_settings WHERE k='token'");
$row = mysqli_fetch_array($result);
$olz_fb_access_token = $row["v"];

$olz_page_id = "946001825452754";
$olz_fb_api_url = "https://graph.facebook.com";
$olz_fb_api_version = "v2.3";
$olz_fb_redirect_url = "http://olzimmerberg.ch/facebook_tools.php";

function fb_api($request, $options="") {
    global $olz_fb_api_url, $olz_fb_api_version, $olz_fb_access_token;
    $ch = curl_init($olz_fb_api_url."/".$olz_fb_api_version."/".$request."?access_token=".$olz_fb_access_token.$options);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file = curl_exec($ch);
    curl_close($ch);
    return json_decode($file, true);
}


if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    $me = fb_api("/me");
    if (isset($me["error"])) {
        $result = $db->query("SELECT v FROM facebook_settings WHERE k='client_id'");
        $row = mysqli_fetch_array($result);
        $olz_fb_client_id = $row["v"];
        if (isset($_GET["code"])) {
            $result = $db->query("SELECT v FROM facebook_settings WHERE k='client_secret'");
            $row = mysqli_fetch_array($result);
            $olz_fb_client_secret = $row["v"];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_URL, $olz_fb_api_url."/".$olz_fb_api_version."/oauth/access_token?client_id=".$olz_fb_client_id."&redirect_uri=".urlencode($olz_fb_redirect_url)."&client_secret=".$olz_fb_client_secret."&code=".$_GET["code"]."&grant_type=authorization_code");
            $file = curl_exec($ch);
            curl_close($ch);
            $resp = json_decode($file, true);
            if (isset($resp["access_token"])) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
                curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 1);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_URL, $olz_fb_api_url."/".$olz_fb_api_version."/".$olz_page_id."?fields=access_token&access_token=".urlencode($resp["access_token"]));
                $file = curl_exec($ch);
                curl_close($ch);
                $resp = json_decode($file, true);
                $db->query("UPDATE facebook_settings SET v='".mysql_real_escape_string($resp["access_token"])."' WHERE k='token'");
                header("Location: ?");
                exit(0);
            }
        } else {
            header("Location: https://www.facebook.com/".$olz_fb_api_version."/dialog/oauth?response_type=code&client_id=".$olz_fb_client_id."&redirect_uri=".urlencode($olz_fb_redirect_url)."&scope=manage_pages");
            exit(0);
        }
    }
    $feed = fb_api("/me/feed");
    $events = fb_api("/me/events");
    $pic = fb_api("/me/picture", "&redirect=false");


    // Statuses
    echo "<h2>Statuses</h2>";
    for ($i=0; $i<count($feed["data"]); $i++) {
        $post = $feed["data"][$i];
        echo "<div>".date("j.n.Y", strtotime($post["updated_time"]))." <a href=''>".$post["from"]["name"]."</a> [".$post["type"]."]";
        if ($post["type"]=="status") {
            echo $post["message"];
        } else if ($post["type"]=="link") {
            echo $post["message"]."<a href='".$post["link"]."'>Link</a>";
        }
        echo "</div>";
    }

    // Events
    echo "<h2>Events</h2>";
    for ($i=0; $i<count($events["data"]); $i++) {
        $event = $events["data"][$i];
        echo "<div>".date("j.n.Y", strtotime($event["start_time"]))." ".$event["name"]."<br>".$event["description"]."</div>";
    }

    echo "<h2>Raw</h2>";
    echo "<pre>";
    print_r($me);
    print_r($feed);
    print_r($events);
    echo "</pre>";

    /*
    // Cover Picture
    echo "<img src='".$me["cover"]["source"]."' />";
    */
    /*
    // Profile Picture
    echo "<img src='".$pic["data"]["url"]."' />";
    */
}
?>