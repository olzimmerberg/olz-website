<?php

namespace Olz\Apps\Panini2024\Utils;

use Fpdf\Fpdf;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Panini2024Utils {
    use WithUtilsTrait;

    public const DPI = 900;
    public const PANINI_SHORT = 42.8; // mm (50.8mm, 4mm margin)
    public const PANINI_LONG = 59; // mm (70mm, 5.5mm margin)
    public const MM_PER_INCH = 25.4;
    public const ASSOCIATION_MAP = [
        'Adliswil' => 'wappen/adliswil.jpg',
        'Einsiedeln' => 'wappen/einsiedeln.jpg',
        'Hirzel' => 'wappen/hirzel.jpg',
        'Horgen' => 'wappen/horgen.jpg',
        'Hütten' => 'wappen/huetten.jpg',
        'Kilchberg' => 'wappen/kilchberg.jpg',
        'Langnau am Albis' => 'wappen/langnau_am_albis.jpg',
        'Oberrieden' => 'wappen/oberrieden.jpg',
        'Richterswil' => 'wappen/richterswil.jpg',
        'Rüschlikon' => 'wappen/rueschlikon.jpg',
        'Schönenberg' => 'wappen/schoenenberg.jpg',
        'Thalwil' => 'wappen/thalwil.jpg',
        'Wädenswil' => 'wappen/waedenswil.jpg',
        'Zürich' => 'wappen/zuerich.jpg',
    ];

    public function renderSingle($id) {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $data_path = $this->envUtils()->getDataPath();
        $panini_path = "{$data_path}panini_data/";
        $masks_path = "{$panini_path}masks/";

        $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
        $picture = $panini_repo->findOneBy(['id' => $id]);
        if (!$picture) {
            throw new NotFoundHttpException("Kein solches Panini vorhanden");
        }
        $owner = $picture->getOwnerUser();
        $is_landscape = $picture->getIsLandscape();
        $has_top = $picture->getHasTop();
        $lp_suffix = $is_landscape ? 'L' : 'P';
        $img_src = $picture->getImgSrc();
        $img_style = $picture->getImgStyle();
        $line1 = $picture->getLine1();
        $line2 = $picture->getLine2();
        $association = $picture->getAssociation();
        $res_wid_percent = preg_match('/width\:\s*([\-0-9\.]+)%/i', $img_style, $matches);
        $img_wid_percent = floatval($res_wid_percent ? $matches[1] : 100);
        $res_top_percent = preg_match('/top\:\s*([\-0-9\.]+)%/i', $img_style, $matches);
        $img_top_percent = floatval($res_top_percent ? $matches[1] : 100);
        $res_left_percent = preg_match('/left\:\s*([\-0-9\.]+)%/i', $img_style, $matches);
        $img_left_percent = floatval($res_left_percent ? $matches[1] : 100);

        $has_panini = $this->authUtils()->hasPermission('panini2024');
        $current_user = $this->authUtils()->getCurrentUser();
        $is_mine = $owner && $current_user && $owner->getId() === $current_user->getId();
        if (!$has_panini && !$is_mine) {
            throw new AccessDeniedHttpException("Kein Zugriff");
        }

        $ident = json_encode([
            $is_landscape,
            $has_top,
            $img_src,
            $img_style,
            $line1,
            $line2,
            $association,
            md5(file_get_contents(__FILE__)),
        ]);
        $md5 = md5($ident);
        $cache_file = "{$panini_path}cache/{$id}-{$md5}.jpg";
        if (is_file($cache_file)) {
            $this->log()->info("Read from cache: {$id}-{$md5}.jpg");
            return file_get_contents($cache_file);
        }

        $wid = round(($is_landscape ? self::PANINI_LONG : self::PANINI_SHORT)
            * self::DPI / self::MM_PER_INCH);
        $hei = round(($is_landscape ? self::PANINI_SHORT : self::PANINI_LONG)
            * self::DPI / self::MM_PER_INCH);
        $img = imagecreatetruecolor($wid, $hei);
        $suffix = "{$lp_suffix}_{$wid}x{$hei}";
        gc_collect_cycles();

        // Payload
        $folder = (intval($id) >= 1000) ? "portraits/{$id}/" : '';
        $payload_path = "{$panini_path}{$folder}{$img_src}";
        $payload_img = imagecreatefromjpeg($payload_path);
        $payload_wid = imagesx($payload_img);
        $payload_hei = imagesy($payload_img);
        imagecopyresampled(
            $img, $payload_img,
            round($img_left_percent * $wid / 100), round($img_top_percent * $hei / 100),
            0, 0,
            round($wid * $img_wid_percent / 100), round($wid * $img_wid_percent * $payload_hei / $payload_wid / 100),
            $payload_wid, $payload_hei,
        );
        gc_collect_cycles();

        // Masks
        $bottom_mask = imagecreatefrompng("{$masks_path}bottom{$suffix}.png");
        imagecopy($img, $bottom_mask, 0, 0, 0, 0, $wid, $hei);
        gc_collect_cycles();
        if ($has_top) {
            $top_mask = imagecreatefrompng("{$masks_path}top{$suffix}.png");
            imagecopy($img, $top_mask, 0, 0, 0, 0, $wid, $hei);
            gc_collect_cycles();
        }

        // Association
        $association_file = self::ASSOCIATION_MAP[$association] ?? null;
        if ($association_file) {
            $association_mask = imagecreatefrompng("{$masks_path}association{$suffix}.png");
            imagecopy($img, $association_mask, 0, 0, 0, 0, $wid, $hei);
            gc_collect_cycles();

            $offset = round(($wid + $hei) * 0.01) - 1;
            $size = round(($wid + $hei) * 0.09) + 1;

            $flag_mask = imagecreatefrompng("{$masks_path}associationStencil{$suffix}.png");

            $association_img = imagecreatetruecolor($size, $size);
            $association_img_orig = imagecreatefromjpeg("{$panini_path}{$association_file}");
            imagecopyresampled(
                $association_img, $association_img_orig,
                0, 0,
                0, 0,
                $size, $size,
                imagesx($association_img_orig), imagesy($association_img_orig),
            );

            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    $mask = imagecolorsforindex($flag_mask,
                        imagecolorat($flag_mask, $x + $offset, $y + $offset));
                    if ($mask['red'] > 0) {
                        $ratio = floatval($mask['red']) / 255.0;
                        $src = imagecolorsforindex($association_img,
                            imagecolorat($association_img, $x, $y));
                        $src_r = floatval($src['red']);
                        $src_g = floatval($src['green']);
                        $src_b = floatval($src['blue']);
                        $dst = imagecolorsforindex($img,
                            imagecolorat($img, $x + $offset, $y + $offset));
                        $dst_r = floatval($dst['red']);
                        $dst_g = floatval($dst['green']);
                        $dst_b = floatval($dst['blue']);
                        $color = imagecolorallocate(
                            $img,
                            intval($src_r * $ratio + $dst_r * (1 - $ratio)),
                            intval($src_g * $ratio + $dst_g * (1 - $ratio)),
                            intval($src_b * $ratio + $dst_b * (1 - $ratio)),
                        );
                        imagesetpixel($img, $x + $offset, $y + $offset, $color);
                        imagecolordeallocate($img, $color);
                    }
                }
            }
            gc_collect_cycles();
        }

        // Text
        $size = $hei * 0.04;
        $yellow = imagecolorallocate($img, 255, 255, 0);
        $shady = imagecolorallocatealpha($img, 0, 0, 0, 64);
        $shoff = $size / 10;
        $font_path = "{$panini_path}fonts/OpenSans/OpenSans-SemiBold.ttf";
        $box = imagettfbbox($size, 0, $font_path, $line1);
        $textwid = $box[2];
        $x = ($line2 ? $wid * 0.95 - $textwid : $wid / 2 - $textwid / 2);
        $y = $hei * ($is_landscape ? 0.95 : ($line2 ? 0.915 : 0.945));
        imagettftext($img, $size, 0, $x + $shoff, $y + $shoff, $shady, $font_path, $line1);
        imagettftext($img, $size, 0, $x, $y, $yellow, $font_path, $line1);
        if ($line2) {
            $box = imagettfbbox($size, 0, $font_path, $line2);
            $textwid = $box[2];
            $x = $wid * 0.95 - $textwid;
            $y = $hei * 0.975;
            imagettftext($img, $size, 0, $x + $shoff, $y + $shoff, $shady, $font_path, $line2);
            imagettftext($img, $size, 0, $x, $y, $yellow, $font_path, $line2);
        }
        gc_collect_cycles();

        ob_start();
        imagejpeg($img, null, 90);
        $image_data = ob_get_contents();
        ob_end_clean();
        imagedestroy($img);
        file_put_contents($cache_file, $image_data);
        $this->log()->info("Written to cache: {$id}-{$md5}.jpg");
        return $image_data;
    }

    public function renderRandom($num, $options) {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
        $all_ids = array_map(function ($picture) {
            return $picture->getId();
        }, $panini_repo->findAll());
        $ids_len = count($all_ids);
        $pages = [];
        for ($p = 0; $p < $num; $p++) {
            $ids = [];
            for ($i = 0; $i < 12; $i++) {
                $ids[] = $all_ids[random_int(0, $ids_len - 1)];
            }
            $pages[] = ['ids' => $ids];
        }
        return $this->renderPages($pages, $options);
    }

    public function renderPages($pages, $options) {
        $data_path = $this->envUtils()->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0777, true);
        }

        $grid = (bool) ($options['grid'] ?? false);
        $x_step = 70;
        $x_margin = 5.5;
        $x_offset = 0;
        $y_step = 50.8;
        $y_offset = 21.5;
        $y_margin = 4;

        foreach ($pages as $page) {
            $ids = $page['ids'] ?? [];
            foreach ($ids as $id) {
                $temp_file_path = "{$temp_path}paninipdf-{$id}.jpg";
                $img = imagecreatefromstring($this->renderSingle($id));
                $wid = imagesx($img);
                $hei = imagesy($img);
                if ($hei > $wid) {
                    $img = imagerotate($img, 90, 0);
                }
                imagejpeg($img, $temp_file_path);
                imagedestroy($img);
                gc_collect_cycles();
            }
        }

        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AliasNbPages();
        foreach ($pages as $page) {
            $ids = $page['ids'] ?? [];
            $pdf->AddPage();

            if ($grid) {
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.1);
                for ($x = 1; $x < 3; $x++) {
                    $pdf->Line(
                        $x_offset + $x_step * $x,
                        $y_offset + $y_step * 0,
                        $x_offset + $x_step * $x,
                        $y_offset + $y_step * 5,
                    );
                }
                for ($y = 0; $y < 6; $y++) {
                    $pdf->Line(
                        $x_offset + $x_step * 0,
                        $y_offset + $y_step * $y,
                        $x_offset + $x_step * 3,
                        $y_offset + $y_step * $y,
                    );
                }
            }

            $index = 0;
            for ($y = 0; $y < 5; $y++) {
                for ($x = 0; $x < 3; $x++) {
                    if ($y !== 2) {
                        $id = $ids[$index] ?? 0;
                        $temp_file_path = "{$temp_path}paninipdf-{$id}.jpg";
                        $pdf->Image(
                            $temp_file_path,
                            $x_offset + $x_margin + $x_step * $x,
                            $y_offset + $y_margin + $y_step * $y,
                            $x_step - $x_margin * 2,
                        );
                        $index++;
                    }
                }
            }
        }
        return $pdf->Output();
    }
}
