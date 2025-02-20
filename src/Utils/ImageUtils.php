<?php

namespace Olz\Utils;

class ImageUtils {
    use WithUtilsTrait;

    public const TABLES_IMG_DIRS = [
        'karten' => 'img/karten/',
        'news' => 'img/news/',
        'questions' => 'img/questions/',
        'roles' => 'img/roles/',
        'snippets' => 'img/snippets/',
        'termine' => 'img/termine/',
        'termin_labels' => 'img/termin_labels/',
        'termin_locations' => 'img/termin_locations/',
        'termin_templates' => 'img/termin_templates/',
        'users' => 'img/users/',
        'weekly_picture' => 'img/weekly_picture/',
    ];

    public function olzImage(
        string $db_table,
        int|string $id,
        string $index,
        int $dim,
        ?string $lightview = 'image',
        string $attrs = '',
    ): string {
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();
        if (!isset($this::TABLES_IMG_DIRS[$db_table])) {
            $message = "Ungültige db_table: {$db_table} (in olzImage)";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
        }
        $db_imgpath = $this::TABLES_IMG_DIRS[$db_table];
        $imgfile = "{$db_imgpath}{$id}/img/{$index}";
        if (!is_file("{$data_path}{$imgfile}")) {
            $message = "Bild nicht vorhanden (in olzImage): {$imgfile}";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
        }
        $info = getimagesize("{$data_path}{$imgfile}");
        $swid = $info[0] ?? 0;
        $shei = $info[1] ?? 0;
        if ($shei < $swid) {
            $wid = $dim;
            $hei = intval($wid * $shei / $swid);
        } else {
            $hei = $dim;
            $wid = intval($hei * $swid / $shei);
        }
        $span_before = $lightview === 'image' ? "<span class='lightgallery'>" : "";
        $span_after = $lightview === 'image' ? "</span>" : "";
        $a_before = $lightview ? "<a href='{$data_href}{$imgfile}' aria-label='Bild vergrössern' data-src='{$data_href}{$imgfile}' onclick='event.stopPropagation()'>" : "";
        $a_after = $lightview ? "</a>" : "";

        $url_without_dim = "{$data_href}img/{$db_table}/{$id}/thumb/{$index}";
        $thumbdim = $this->getThumbSize($dim);
        $thumbdim2x = $thumbdim * 2;
        return <<<ZZZZZZZZZZ
            {$span_before}{$a_before}
            <img
                src='{$url_without_dim}\${$thumbdim}.jpg'
                srcset='{$url_without_dim}\${$thumbdim2x}.jpg 2x, {$url_without_dim}\${$thumbdim}.jpg 1x'
                alt=''
                width='{$wid}'
                height='{$hei}'
                {$attrs}
            />
            {$a_after}{$span_after}
            ZZZZZZZZZZ;
    }

    public function getThumbSize(int $size): int {
        $ndim = $size - 1;
        for ($i = 1; $i < 9 && ($ndim >> $i) > 0; $i++) {
        }
        return 1 << $i;
    }

    /** @param array<string> $image_ids */
    public function generateThumbnails(array $image_ids, string $entity_img_path): void {
        foreach ($image_ids as $image_id) {
            for ($i = 5; $i < 9; $i++) {
                $size = (1 << $i);
                if (!is_file("{$entity_img_path}thumb/{$image_id}\${$size}.jpg")) {
                    $this->log()->info("Generate {$entity_img_path}thumb/{$image_id}\${$size}.jpg...");
                    $this->getThumbFile($image_id, $entity_img_path, $size);
                }
            }
        }
    }

    public function getThumbFile(string $image_id, string $entity_img_path, int $size): string {
        if ($size !== 32 && $size !== 64 && $size !== 128 && $size !== 256 && $size !== 512) {
            throw new \Exception("Size must be a power of two (32,64,128,256,512), was: {$size}");
        }
        $imgfile = "{$entity_img_path}img/{$image_id}";
        $info = getimagesize($imgfile);
        $swid = $info[0] ?? 0;
        $shei = $info[1] ?? 0;
        if ($shei < $swid) {
            $wid = $size;
            $hei = intval($wid * $shei / $swid);
        } else {
            $hei = $size;
            $wid = intval($hei * $swid / $shei);
        }
        if ($wid <= 0 || $hei <= 0 || $wid > 800 || $hei > 800) {
            $message = "getThumbFile: Invalid dimension: {$size}";
            $this->log()->warning($message);
            throw new \Exception($message);
        }
        if ($wid > 256 || $hei > 256) {
            $thumbfile = $imgfile;
        } else {
            $thumbfile = "{$entity_img_path}thumb/{$image_id}\${$size}.jpg";
        }
        if (!is_file($thumbfile)) {
            if (!is_dir(dirname($thumbfile))) {
                mkdir(dirname($thumbfile), 0o777, true);
            }
            $img = imagecreatefromjpeg($imgfile);
            if (!$img) {
                $message = "getThumbFile: Could not open image {$imgfile}";
                $this->log()->warning($message);
                throw new \Exception($message);
            }
            $thumb = imagecreatetruecolor($wid, $hei);
            imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $wid, $hei, $swid, $shei);
            imagejpeg($thumb, $thumbfile, 90);
            imagedestroy($thumb);
        }
        return $thumbfile;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
