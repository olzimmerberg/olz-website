<?php

require_once("admin/olz_init.php");
require_once("./upload_tools.php");

$tables_file_dirs = array(
    "aktuell"=>"files/aktuell/",
    "blog"=>"files/blog/",
    "downloads"=>"files/downloads/"
);

$mime_extensions = array(
    "text/csv"=>"csv",
    "text/html"=>"html",
    "text/plain"=>"txt",
    "text/rtf"=>"rtf",
    "text/vcard"=>"vcf",
    "text/xml"=>"xml",
    "application/pdf"=>"pdf",
    "application/msexcel"=>"xls",
    "application/x-msexcel"=>"xls",
    "application/x-ms-excel"=>"xls",
    "application/x-excel"=>"xls",
    "application/x-dos_ms_excel"=>"xls",
    "application/xls"=>"xls",
    "application/x-xls"=>"xls",
    "application/msword"=>"doc",
    "application/vnd.ms-excel"=>"xls",
    "application/vnd.ms-powerpoint"=>"ppt",
    "application/vnd.openxmlformats-officedocument.presentationml.presentation"=>"pptx",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>"xlsx",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>"docx",
    "application/x-pdf"=>"pdf",
    "application/zip"=>"zip",
    "image/gif"=>"gif",
    "image/jpeg"=>"jpg",
    "image/png"=>"png",
);

$extension_icons = array(
    "csv"=>"txt",
    "doc"=>"doc",
    "docx"=>"doc",
    "gif"=>"image",
    "html"=>"html",
    "jpg"=>"image",
    "pdf"=>"pdf",
    "png"=>"image",
    "ppt"=>"ppt",
    "pptx"=>"ppt",
    "rtf"=>"txt",
    "txt"=>"txt",
    "vcf"=>"txt",
    "xml"=>"txt",
    "xls"=>"xls",
    "xlsx"=>"xls",
    "zip"=>"zip",
);


if (basename($_SERVER["SCRIPT_FILENAME"]) == basename(__FILE__)) {
    session_start();
    if (!isset($_GET["request"])) {
        header("Content-type:text/plain");
        echo "HIER IST NIX";
        print_r($_SESSION);
    }

    if ($_GET["request"]=="thumb") {
        $db_table = $_GET["db_table"];
        if (!isset($tables_file_dirs[$db_table])) {echo "Invalid db_table (in thumb)"; return;}
        $db_filepath = $tables_file_dirs[$db_table];
        $id = intval($_GET["id"]);
        if ($id<=0) {echo "Invalid id (in thumb)"; return;}
        $index = intval($_GET["index"]);
        if ($index<=0) {echo "Invalid index (in thumb)"; return;}
        $dim = 16;
        if (16<intval($_GET["dim"])) $dim = 128;
        $files = scandir($data_path.$db_filepath."/".$id);
        for ($i=0; $i<count($files); $i++) {
            if (preg_match("/^([0-9]{3})\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                if (intval($matches[1])==$index) {
                    $thumbfile = $data_path."img/fileicons/".$extension_icons[$matches[2]]."-".$dim.".png";
                    if (!is_file($thumbfile)) $thumbfile = $data_path."img/fileicons/unknown-".$dim.".png";
                    header("Content-Type:image/png");
                    $fp = fopen($thumbfile, "r");
                    $buf = fread($fp, 1024);
                    while ($buf) {
                        echo $buf;
                        $buf = fread($fp, 1024);
                    }
                    fclose($fp);
                }
            }
        }
    }

    if ($_GET["request"]=="info") {
        $db_table = $_GET["db_table"];
        if (!isset($tables_file_dirs[$db_table])) {echo "Invalid db_table"; return;}
        $db_filepath = $tables_file_dirs[$db_table];
        $id = intval($_GET["id"]);
        $files = scandir($data_path.$db_filepath."/".$id);
        $indices = array();
        for ($i=0; $i<count($files); $i++) {
            if (preg_match("/^([0-9]{3})\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                $indices[intval($matches[1])] = true;
            }
        }
        $newindex = 0;
        for ($i=0; true; $i++) {
            if (!isset($indices[$i+1])) {
                $newindex = $i;
                break;
            }
        }
        echo json_encode(array("count"=>$newindex));
    }

    if ($_GET["request"]=="uploadpart") {
        // Data sanitization & initialization
        $db_table = $_GET["db_table"];
        if (!isset($tables_file_dirs[$db_table])) {echo json_encode(array(0, "!tables_dirs-dbtable")); return;}
        $db_filepath = $tables_file_dirs[$db_table];
        $id = intval($_GET["id"]);
        if ($id<=0) {echo json_encode(array(0, "!id")); return;}
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"]!=$id) {echo json_encode(array(0, "!permission")); return;}
        $part = intval($_POST["part"]);
        if ($part<0 || 100<$part) {echo json_encode(array(0, "!part<100")); return;}
        $temppath = "temp/".md5($data_path.$db_filepath)."-".$id."-".$part;
        $fp = fopen($temppath, "w+");
        fwrite($fp, deobfuscate_upload($_POST["content"]));
        fclose($fp);

        if ($_POST["last"]=="1") {
            // Create directory structure, if not yet present
            $abspath = $data_path.$db_filepath."/".$id;
            if (!is_dir($abspath."/")) mkdir($abspath."/");

            $temppath = "temp/".md5($data_path.$db_filepath)."-".$id."-";
            $firstcontent = file_get_contents($temppath."0");
            @unlink($temppath."0");
            $res = preg_match("/^data\:([^\;]*)\;base64\,(.+)$/", $firstcontent, $matches);
            if (!$res) {echo json_encode(array(0,"!res")); return;}
            if (!isset($mime_extensions[$matches[1]])) {
                $res_fn = preg_match("/\.([^\.]*)$/", $_POST["filename"], $matches_fn);
                if (!isset($extension_icons[$matches_fn[1]])) {echo json_encode(array(0, "!mime:".$matches[1]." - !Filename-Extension:".$_POST["filename"])); return;}
                $ext = $matches_fn[1];
            } else {
                $ext = $mime_extensions[$matches[1]];
            }
            $base64 = $matches[2];
            for ($i=1; $i<=$part; $i++) {
                if (!is_file($temppath.$i)) {echo json_encode(array(0, "!part".$i)); return;}
                $partcontent = file_get_contents($temppath.$i);
                $base64 .= $partcontent;
                @unlink($temppath.$i);
            }
            $filedata = base64_decode(str_replace(" ", "+", $base64));
            if (!$filedata) {echo json_encode(array(0,"!filedata")); return;}
            $files = scandir($abspath);
            $indices = array();
            for ($i=0; $i<count($files); $i++) {
                if (preg_match("/^([0-9]{3})\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                    $indices[intval($matches[1])] = true;
                }
            }
            $newindex = 0;
            for ($i=0; true; $i++) {
                if (!isset($indices[$i+1])) {
                    $newindex = $i;
                    break;
                }
            }

            // Write uploaded file to destination
            $fp = fopen($abspath."/".str_pad(($newindex+1), 3, "0", STR_PAD_LEFT).".".$ext, "w+");
            fwrite($fp, $filedata);
            fclose($fp);

            echo json_encode(array(1, str_pad(($newindex+1), 3, "0", STR_PAD_LEFT), $files, strlen($base64), strlen($filedata)));
        } else {
            echo json_encode(array(1, "continue"));
        }
    }

    if ($_GET["request"]=="change") {
        $db_table = $_GET["db_table"];
        if (!isset($tables_file_dirs[$db_table])) {echo json_encode(array(0,"!tables_dirs-dbtable")); return;}
        $id = intval($_POST["id"]);
        if ($id<=0) {echo json_encode(array(0,"!id")); return;}
        if (!isset($_SESSION[$db_table."id"]) || $_SESSION[$db_table."id"]!=$id) {echo json_encode(array(0,"!permission")); return;}
        $index = intval($_POST["index"]);
        if ($index<=0) {echo json_encode(array(0,"!index")); return;}
        $db_filepath = $tables_file_dirs[$db_table];
        if ($_POST["delete"]==1) {
            $files = scandir($data_path.$db_filepath."/".$id);
            for ($i=0; $i<count($files); $i++) {
                if (preg_match("/^([0-9]{3})\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                    if (intval($matches[1])==$index) {
                        @unlink($data_path.$db_filepath."/".$id."/".$matches[0]);
                    } else if ($index<intval($matches[1])) {
                        rename(
                            $data_path.$db_filepath."/".$id."/".$matches[0],
                            $data_path.$db_filepath."/".$id."/".str_pad(intval($matches[1])-1, 3, "0", STR_PAD_LEFT).".".$matches[2]
                        );
                    }
                }
            }
        }
        echo json_encode(array(1, intval($_POST["delete"])));
    }
}

function olz_file($db_table, $id, $index, $text, $icon="mini") {
    global $data_href, $data_path, $tables_file_dirs;
    if (!isset($tables_file_dirs[$db_table])) return "Ungültige db_table (in olz_file)";
    $db_filepath = $tables_file_dirs[$db_table];
    if (is_dir($data_path.$db_filepath."/".$id)) {
        $files = scandir($data_path.$db_filepath."/".$id);
        for ($i=0; $i<count($files); $i++) {
            if (preg_match("/^([0-9]{3})\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                if (intval($matches[1])==$index) {
                    return "<a href='".$data_href.$db_filepath."/".$id."/".$matches[0]."?modified=".filemtime($data_path.$db_filepath."/".$id."/".$files[$i])."'".($icon=="mini"?" style='padding-left:19px; background-image:url(file_tools.php?request=thumb&db_table=".$db_table."&id=".$id."&index=".$index."&dim=16); background-repeat:no-repeat;'":"").">".$text."</a>";
                }
            }
        }
    } else {
        return "<span style='color:#ff0000; font-style:italic;'>!is_dir ".$db_filepath."/".$id."</span>";
    }
    return "<span style='color:#ff0000; font-style:italic;'>Datei nicht vorhanden (in olz_file)</span>";
}

function olz_files_edit($db_table, $id) {
    global $tables_file_dirs;
    if (!isset($tables_file_dirs[$db_table])) return "Ungültige db_table (in olz_files_edit)";
    $db_filepath = $tables_file_dirs[$db_table];
    $htmlout = "";
    $ident = "olzfileedit".md5($db_table."-".$id);
    $htmlout .= "<div id='".$ident."'></div>";
    $htmlout .= "<script type='text/javascript' src='scripts/file_tools.js?version=2'></script>";
    $htmlout .= "<script type='text/javascript' src='scripts/upload_tools.js?version=2'></script>";
    $htmlout .= "<script type='text/javascript'>olz_files_edit_redraw(".json_encode($ident).", ".json_encode($db_table).", ".json_encode($id).");</script>";
    return $htmlout;
}

?>
