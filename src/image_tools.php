<?php

// =============================================================================
// Funktionen für Bild-Upload, z.B. Bilder in Aktuell-Einträgen.
// =============================================================================

require_once __DIR__.'/config/init.php';
require_once __DIR__."/config/paths.php";
require_once __DIR__."/utils/GeneralUtils.php";

global $tables_img_dirs;

$tables_img_dirs = [
    "galerie" => "img/galerie/",
    "aktuell" => "img/aktuell/",
    "bild_der_woche" => "img/bild_der_woche/",
    "blog" => "img/blog/",
];

if (basename($_SERVER["SCRIPT_FILENAME"] ?? '') == basename(__FILE__)) {
    if (!isset($_GET["request"])) {
        header("Content-type:text/plain");
        echo "HIER IST NIX";
    }

    if ($_GET["request"] == "thumb") {
        session_write_close();
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo "Invalid db_table";
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        $imgfile = $data_path.$db_imgpath."/".intval($_GET["id"])."/img/".str_pad(intval($_GET["index"]), 3, "0", STR_PAD_LEFT).".jpg";
        if (is_file($imgfile)) {
            $ndim = intval($_GET["dim"]) - 1;
            $dim = false;
            for ($i = 1; $i < 9 && ($ndim >> $i) > 0; $i++);
            $dim = (1 << $i);
            if ($dim < 16) {
                $dim = 16;
            }
            $info = getimagesize($imgfile);
            $swid = $info[0];
            $shei = $info[1];
            if ($shei < $swid) {
                $wid = $dim;
                $hei = intval($wid * $shei / $swid);
            } else {
                $hei = $dim;
                $wid = intval($hei * $swid / $shei);
            }
            if ($wid <= 0 || $hei <= 0 || $wid > 800 || $hei > 800) {
                echo "Invalid dim";
                return;
            }
            if ($wid > 256 || $hei > 256) {
                $thumbfile = $imgfile;
            } else {
                $thumbfile = $data_path.$db_imgpath."/".intval($_GET["id"])."/thumb/".str_pad(intval($_GET["index"]), 3, "0", STR_PAD_LEFT)."_".$wid."x".$hei.".jpg";
            }
            if (!is_file($thumbfile)) {
                if (!is_dir(dirname($thumbfile))) {
                    mkdir(dirname($thumbfile), 0777, true);
                }
                $img = imagecreatefromjpeg($imgfile);
                $thumb = imagecreatetruecolor($wid, $hei);
                imagesavealpha($thumb, true);
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $wid, $hei, $swid, $shei);
                imagejpeg($thumb, $thumbfile, 90);
                imagedestroy($thumb);
            }
            header("Cache-Control: max-age=2592000");
            header("Content-Type: image/jpeg");
            $fp = fopen($thumbfile, "r");
            $buf = fread($fp, 1024);
            while ($buf) {
                echo $buf;
                $buf = fread($fp, 1024);
            }
            fclose($fp);
        }
    }

    if ($_GET["request"] == "info") {
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo "Invalid db_table";
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        $id = intval($_GET["id"]);
        for ($i = 1; true; $i++) {
            $imgfile = $data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg";
            if (!is_file($imgfile)) {
                break;
            }
        }
        echo json_encode(["count" => $i - 1]);
    }

    if ($_GET["request"] == "uploadresized") {
        session_start();
        // Data sanitization & initialization
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo json_encode([0, "!tables_dirs-dbtable"]);
            return;
        }
        $id = intval($_GET["id"]);
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"] != $id) {
            echo json_encode([0, "!permission"]);
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        $general_utils = GeneralUtils::fromEnv();
        $content = $general_utils->deobfuscateUpload(str_replace(" ", "+", $_POST["content"]));
        $res = preg_match("/\\;base64\\,(.+)$/", $content, $matches);
        if (!$res) {
            echo json_encode([0, "!res"]);
            return;
        }
        $imgdata = base64_decode(str_replace(" ", "+", $matches[1]));
        if (!$imgdata) {
            echo json_encode([0, "!imgdata"]);
            return;
        }
        if (!is_numeric($_GET["id"])) {
            echo json_encode([0, "!id"]);
            return;
        }
        $filename = $data_path."temp/".md5(time().$imgdata);
        $abspath = $data_path.$db_imgpath."/".$id;
        $newindex = 0;
        for ($i = 0; true; $i++) {
            if (!is_file($abspath."/img/".str_pad(($i + 1), 3, "0", STR_PAD_LEFT).".jpg")) {
                $newindex = $i;
                break;
            }
        }

        // Write uploaded (pre-resized) image to temp/
        $fp = fopen($filename, "w+");
        fwrite($fp, $imgdata);
        fclose($fp);

        // Create directory structure, if not yet present
        if (!is_dir($abspath."/")) {
            mkdir($abspath."/");
        }
        if (!is_dir($abspath."/img")) {
            mkdir($abspath."/img");
        }
        if (!is_dir($abspath."/thumb")) {
            mkdir($abspath."/thumb");
        }

        // Read metadata and the uploaded image
        $pathinfo = pathinfo($filename);
        $info = getimagesize($filename);
        $swid = $info[0];
        $shei = $info[1];
        if ($shei < $swid) {
            $wid = 1;
            $hei = $shei / $swid;
        } else {
            $wid = $swid / $shei;
            $hei = 1;
        }
        if ($info[2] == IMAGETYPE_JPEG) {
            $source = imagecreatefromjpeg($filename);
        } elseif ($info[2] == IMAGETYPE_PNG) {
            $source = imagecreatefrompng($filename);
        }

        // Create Full-size image
        $maxdim = 800;
        if ($info[2] == IMAGETYPE_JPEG && $wid * $maxdim >= $swid && $hei * $maxdim >= $shei) {
            copy($filename, $abspath."/img/".str_pad(($newindex + 1), 3, "0", STR_PAD_LEFT).".jpg");
        } else {
            $img = imagecreatetruecolor($wid * $maxdim, $hei * $maxdim);
            imagesavealpha($img, true);
            imagecopyresampled($img, $source, 0, 0, 0, 0, $wid * $maxdim, $hei * $maxdim, $swid, $shei);
            imagejpeg($img, $abspath."/img/".str_pad(($newindex + 1), 3, "0", STR_PAD_LEFT).".jpg", 90);
            imagedestroy($img);
        }

        imagedestroy($source);
        unlink($filename);
        if (is_dir($data_path.$db_imgpath."/".$id."/thumb")) {
            $imgs = scandir($data_path.$db_imgpath."/".$id."/thumb");
            for ($i = 0; $i < count($imgs); $i++) {
                if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                    @unlink($data_path.$db_imgpath."/".$id."/thumb/".$imgs[$i]);
                }
            }
        }
        echo json_encode([1, str_pad(($newindex + 1), 3, "0", STR_PAD_LEFT), $info]);
    }

    if ($_GET["request"] == "change") {
        session_start();
        $index = intval($_POST["index"]);
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo json_encode([0, "!tables_dirs-dbtable"]);
            return;
        }
        $id = intval($_POST["id"]);
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"] != $id) {
            echo json_encode([0, "!permission"]);
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        if ($_POST["delete"] == 1) {
            @unlink($data_path.$db_imgpath."/".$id."/img/".str_pad($index, 3, "0", STR_PAD_LEFT).".jpg");
            if (is_dir($data_path.$db_imgpath."/".$id."/thumb")) {
                $imgs = scandir($data_path.$db_imgpath."/".$id."/thumb");
                for ($i = 0; $i < count($imgs); $i++) {
                    if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                        @unlink($data_path.$db_imgpath."/".$id."/thumb/".$imgs[$i]);
                    }
                }
            }
            for ($i = $index + 1; is_file($data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg"); $i++) {
                rename(
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg",
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i - 1, 3, "0", STR_PAD_LEFT).".jpg"
                );
            }
        } else {
            $img_path = $data_path.$db_imgpath."/".$id."/img/".str_pad($index, 3, "0", STR_PAD_LEFT).".jpg";
            $img = imagecreatefromjpeg($img_path);
            $img = imagerotate($img, -intval($_POST["rotate"]), 0);
            imagejpeg($img, $img_path, 90);
            if (is_dir($data_path.$db_imgpath."/".$id."/thumb")) {
                $imgs = scandir($data_path.$db_imgpath."/".$id."/thumb");
                for ($i = 0; $i < count($imgs); $i++) {
                    if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                        @unlink($data_path.$db_imgpath."/".$id."/thumb/".$imgs[$i]);
                    }
                }
            }
        }
        echo json_encode([1, intval($_POST["delete"]), intval($_POST["rotate"])]);
    }

    if ($_GET["request"] == "reorder") {
        session_start();
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo json_encode([0, "!tables_dirs-dbtable"]);
            return;
        }
        $id = intval($_POST["id"]);
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"] != $id) {
            echo json_encode([0, "!permission"]);
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        $from = intval($_POST["from"]);
        if ($from <= 0) {
            echo json_encode([0, "!from"]);
            return;
        }
        $to = intval($_POST["to"]);
        if ($to <= 0) {
            echo json_encode([0, "!to"]);
            return;
        }
        $log = "";
        rename(
            $data_path.$db_imgpath."/".$id."/img/".str_pad($from, 3, "0", STR_PAD_LEFT).".jpg",
            $data_path.$db_imgpath."/".$id."/img/tmp.jpg"
        );
        if ($from < $to) {
            for ($i = $from + 1; $i < $to; $i++) {
                rename(
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg",
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i - 1, 3, "0", STR_PAD_LEFT).".jpg"
                );
                $log .= ", ".$i."=>".($i - 1);
            }
            rename(
                $data_path.$db_imgpath."/".$id."/img/tmp.jpg",
                $data_path.$db_imgpath."/".$id."/img/".str_pad($to - 1, 3, "0", STR_PAD_LEFT).".jpg"
            );
            $log .= ", ".$from."=>".($to - 1);
        } else {
            for ($i = $from; $i > $to; $i--) {
                rename(
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i - 1, 3, "0", STR_PAD_LEFT).".jpg",
                    $data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg"
                );
                $log .= ", ".($i - 1)."=>".($i);
            }
            rename(
                $data_path.$db_imgpath."/".$id."/img/tmp.jpg",
                $data_path.$db_imgpath."/".$id."/img/".str_pad($to, 3, "0", STR_PAD_LEFT).".jpg"
            );
            $log .= ", ".$from."=>".$to;
        }
        if (is_dir($data_path.$db_imgpath."/".$id."/thumb")) {
            $imgs = scandir($data_path.$db_imgpath."/".$id."/thumb");
            for ($i = 0; $i < count($imgs); $i++) {
                if ($imgs[$i] != ".." && $imgs[$i] != ".") {
                    @unlink($data_path.$db_imgpath."/".$id."/thumb/".$imgs[$i]);
                }
            }
        }
        echo json_encode([1, $log]);
    }

    if ($_GET["request"] == "merge") {
        session_start();
        $db_table = $_GET["db_table"];
        if (!isset($tables_img_dirs[$db_table])) {
            echo json_encode([0, "!tables_dirs-dbtable"]);
            return;
        }
        $fromid = intval($_POST["fromid"]);
        $id = intval($_POST["id"]);
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"] != $id) {
            echo json_encode([0, "!permission"]);
            return;
        }
        $db_imgpath = $tables_img_dirs[$db_table];
        $newindex = 0;
        for ($i = 0; true; $i++) {
            if (!is_file($data_path.$db_imgpath."/".$id."/img/".str_pad(($i + 1), 3, "0", STR_PAD_LEFT).".jpg")) {
                $newindex = $i;
                break;
            }
        }
        $log = "";
        for ($i = 0; true; $i++) {
            if (is_file($data_path.$db_imgpath."/".$fromid."/img/".str_pad(($i + 1), 3, "0", STR_PAD_LEFT).".jpg")) {
                copy(
                    $data_path.$db_imgpath."/".$fromid."/img/".str_pad(($i + 1), 3, "0", STR_PAD_LEFT).".jpg",
                    $data_path.$db_imgpath."/".$id."/img/".str_pad(($newindex + $i + 1), 3, "0", STR_PAD_LEFT).".jpg"
                );
            } else {
                break;
            }
        }
        echo json_encode([1, $newindex + $i, $log]);
    }
}

function olz_image($db_table, $id, $index, $dim, $lightview = "image", $attrs = "") {
    global $data_href, $data_path, $tables_img_dirs;
    if (!isset($tables_img_dirs[$db_table])) {
        return "Ungültige db_table: {$db_table} (in olz_image)";
    }
    $db_imgpath = $tables_img_dirs[$db_table];
    $imgfile = $db_imgpath."/".$id."/img/".str_pad(intval($index), 3, "0", STR_PAD_LEFT).".jpg";
    if (!is_file($data_path.$imgfile)) {
        return "Bild nicht vorhanden (in olz_image)";
    }
    $info = getimagesize($data_path.$imgfile);
    $swid = $info[0];
    $shei = $info[1];
    if ($shei < $swid) {
        $wid = $dim;
        $hei = intval($wid * $shei / $swid);
    } else {
        $hei = $dim;
        $wid = intval($hei * $swid / $shei);
    }
    $span_before = $lightview == "image" ? "<span class='lightgallery'>" : "";
    $span_after = $lightview == "image" ? "</span>" : "";
    $a_before = $lightview ? "<a href='".$data_href.$imgfile."' aria-label='Bild vergrössern' data-src='".$data_href.$imgfile."'>" : "";
    $a_after = $lightview ? "</a>" : "";
    return $span_before.$a_before."<img src='image_tools.php?request=thumb&db_table=".$db_table."&id=".$id."&index=".$index."&dim=".$dim."' alt='' width='".$wid."' height='".$hei."'".$attrs.">".$a_after.$span_after;
}

function olz_images_edit($db_table, $id) {
    global $data_path, $tables_img_dirs;
    if (!isset($tables_img_dirs[$db_table])) {
        return "Ungültige db_table (in olz_images_edit)";
    }
    $db_imgpath = $tables_img_dirs[$db_table];
    for ($i = 1; true; $i++) {
        $imgfile = $data_path.$db_imgpath."/".$id."/img/".str_pad($i, 3, "0", STR_PAD_LEFT).".jpg";
        if (!is_file($imgfile)) {
            break;
        }
    }
    $htmlout = "";
    $ident = "olzimgedit".md5($db_table."-".$id);
    $htmlout .= "<div id='".$ident."'></div>";
    $htmlout .= "<script type='text/javascript'>olz_images_edit_redraw(".json_encode($ident).", ".json_encode($db_table).", ".json_encode($id).");</script>";
    return $htmlout;
}
