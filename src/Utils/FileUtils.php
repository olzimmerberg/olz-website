<?php

namespace Olz\Utils;

class FileUtils {
    use WithUtilsTrait;

    public const TABLES_FILE_DIRS = [
        'downloads' => 'files/downloads/',
        'news' => 'files/news/',
        'termine' => 'files/termine/',
        'termin_templates' => 'files/termin_templates/',
    ];

    public const MIME_EXTENSIONS = [
        'text/csv' => 'csv',
        'text/html' => 'html',
        'text/plain' => 'txt',
        'text/rtf' => 'rtf',
        'text/vcard' => 'vcf',
        'text/xml' => 'xml',
        'application/pdf' => 'pdf',
        'application/msexcel' => 'xls',
        'application/x-msexcel' => 'xls',
        'application/x-ms-excel' => 'xls',
        'application/x-excel' => 'xls',
        'application/x-dos_ms_excel' => 'xls',
        'application/xls' => 'xls',
        'application/x-xls' => 'xls',
        'application/msword' => 'doc',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/x-pdf' => 'pdf',
        'application/zip' => 'zip',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public const EXTENSION_ICONS = [
        'csv' => 'txt',
        'doc' => 'doc',
        'docx' => 'doc',
        'gif' => 'image',
        'html' => 'html',
        'jpg' => 'image',
        'pdf' => 'pdf',
        'png' => 'image',
        'ppt' => 'ppt',
        'pptx' => 'ppt',
        'rtf' => 'txt',
        'txt' => 'txt',
        'vcf' => 'txt',
        'xml' => 'txt',
        'xls' => 'xls',
        'xlsx' => 'xls',
        'zip' => 'zip',
    ];

    public function replaceFileTags($text, $db_table, $id, $download_name, $icon = 'mini') {
        preg_match_all("/<datei([0-9]+|\\=[0-9A-Za-z_\\-]{24}\\.\\S{1,10})(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $index = $matches[1][$i];
            $is_migrated = !(is_numeric($index) && intval($index) > 0 && intval($index) == $index);
            $tmptext = $matches[4][$i];
            if (mb_strlen($tmptext) < 1) {
                $tmptext = "Datei {$index}";
            }

            if ($is_migrated) {
                $new_html = $this->olzFile(
                    $db_table == 'aktuell' ? 'news' : $db_table,
                    $id,
                    substr($index, 1),
                    $tmptext,
                    "{$download_name} - {$tmptext}",
                    $icon
                );
            } else {
                // TODO: Delete this monster-logic!
                $is_blog = $db_table !== 'termine' && $id >= 6400 && $id < 6700;
                $new_html = $this->olzFile(
                    $is_blog ? 'blog' : ($db_table == 'news' ? 'aktuell' : $db_table),
                    $is_blog ? $id - 6400 : $id,
                    intval($index),
                    $tmptext,
                    "{$download_name} - {$tmptext}",
                    $icon
                );
            }
            $text = str_replace($matches[0][$i], $new_html, $text);
        }
        return $text;
    }

    public function olzFile($db_table, $id, $index, $text, $download_name, $icon = 'mini') {
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();
        if (!isset($this::TABLES_FILE_DIRS[$db_table])) {
            $message = "Ungültige db_table: {$db_table} (in olzFile)";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
        }
        $is_migrated = !(is_numeric($index) && intval($index) > 0 && intval($index) == $index);
        $db_filepath = $this::TABLES_FILE_DIRS[$db_table];
        $file_dir = "{$data_path}{$db_filepath}/{$id}";
        if (!is_dir($file_dir)) {
            $message = "!is_dir {$db_filepath}/{$id}";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
        }
        if ($is_migrated) {
            if (is_file("{$file_dir}/{$index}")) {
                $filemtime = filemtime("{$file_dir}/{$index}");
                $extension = $this->uploadUtils()->getExtension($index);
                $url = "{$data_href}{$db_filepath}/{$id}/{$index}?modified={$filemtime}";
                return "<span class='rendered-markdown'><a href='{$url}' download='{$download_name}{$extension}'>{$text}</a></span>";
            }
        }
        $this->log()->notice("Unmigrated file: {$index} ({$db_table}/{$id})");
        $files = scandir($file_dir);
        for ($i = 0; $i < count($files); $i++) {
            if (preg_match("/^([0-9]{3})\\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                if (intval($matches[1]) == $index && is_file("{$file_dir}/{$files[$i]}")) {
                    $filemtime = @filemtime("{$file_dir}/{$files[$i]}");
                    $style = ($icon == "mini" ? " style='padding-left:19px; background-image:url({$code_href}file_tools/thumb/{$db_table}\${$id}\${$index}\$16.svg); background-repeat:no-repeat;'" : "");
                    return "<a href='{$data_href}{$db_filepath}/{$id}/{$matches[0]}?modified={$filemtime}'{$style}>{$text}</a>";
                }
            }
        }
        $message = "Datei nicht vorhanden (in olzFile): {$db_filepath}/{$id}/{$index}";
        $this->log()->error($message);
        return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
    }
}
