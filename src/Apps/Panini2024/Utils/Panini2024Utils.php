<?php

namespace Olz\Apps\Panini2024\Utils;

use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Panini2024Utils {
    use WithUtilsTrait;

    public const DPI = 900;
    public const MM_PER_INCH = 25.4;

    // This is the version with the self-printed foldable label paper:
    // public const PANINI_SHORT = 42.8; // mm (50.8mm, 4mm margin)
    // public const PANINI_LONG = 59; // mm (70mm, 5.5mm margin)

    // This is the version with 4x4 pictures per A4, which need to be cut:
    public const PANINI_SHORT = 45; // mm
    public const PANINI_LONG = 65; // mm

    public const ASSOCIATION_MAP = [
        'Au ZH' => 'wappen/waedenswil.jpg',
        'Adliswil' => 'wappen/adliswil.jpg',
        'Hirzel' => 'wappen/hirzel.jpg',
        'Horgen' => 'wappen/horgen.jpg',
        'Kilchberg' => 'wappen/kilchberg.jpg',
        'Langnau am Albis' => 'wappen/langnau_am_albis.jpg',
        'Oberrieden' => 'wappen/oberrieden.jpg',
        'Richterswil' => 'wappen/richterswil.jpg',
        'Rüschlikon' => 'wappen/rueschlikon.jpg',
        'Samstagern' => 'wappen/richterswil.jpg',
        'Schönenberg' => 'wappen/waedenswil.jpg',
        'Thalwil' => 'wappen/thalwil.jpg',
        'Wädenswil' => 'wappen/waedenswil.jpg',
        'Zürich' => 'wappen/zuerich.jpg',

        'Einsiedeln' => 'wappen/einsiedeln.jpg',
        'Küsnacht ZH' => 'wappen/kuesnacht_zh.jpg',
        'Oberwil' => 'wappen/daegerlen.jpg',
        'Maur' => 'wappen/maur.jpg',
        'Niederurnen GL' => 'wappen/niederurnen.jpg',
        'Pfäffikon ZH' => 'wappen/pfaeffikon_zh.jpg',
        'Basel' => 'wappen/basel.jpg',
        'Hausen am Albis' => 'wappen/hausen_am_albis.jpg',
        'Wollerau' => 'wappen/wollerau.jpg',
        'Riedikon' => 'wappen/uster.jpg',
        'Winterthur' => 'wappen/winterthur.jpg',
        'Landquart GR' => 'wappen/landquart_gr.jpg',
        'Wolhusen LU' => 'wappen/wolhusen_lu.jpg',
        'Wetzikon' => 'wappen/wetzikon.jpg',
        'Affoltern am Albis' => 'wappen/affoltern_am_albis.jpg',
        'Greifensee' => 'wappen/greifensee.jpg',
        'St. Gallen' => 'wappen/st_gallen.jpg',
        'Bern' => 'wappen/bern.jpg',
        'Seewen SZ' => 'wappen/seewen_sz.jpg',
    ];

    public function parseSpec($spec, $num_per_page) {
        $random_res = preg_match('/^random-([0-9]+)(-grid)?$/i', $spec, $random_matches);
        if ($random_res) {
            $num = intval($random_matches[1]);
            $options = [
                'grid' => ($random_matches[2] ?? '') === '-grid',
            ];
            $panini_repo = $this->entityManager()->getRepository(Panini2024Picture::class);
            $all_ids = array_map(function ($picture) {
                return $picture->getId();
            }, $panini_repo->findAll());
            $ids_len = count($all_ids);
            $pages = [];
            for ($p = 0; $p < $num; $p++) {
                $ids = [];
                for ($i = 0; $i < 16; $i++) {
                    $ids[] = $all_ids[random_int(0, $ids_len - 1)];
                }
                $pages[] = ['ids' => $ids];
            }
            return [$pages, $options];
        }
        $duplicate_res = preg_match('/^duplicate-([0-9]+)(-grid)?$/i', $spec, $duplicate_matches);
        if ($duplicate_res) {
            $id = intval($duplicate_matches[1]);
            $ids = [];
            for ($i = 0; $i < $num_per_page; $i++) {
                $ids[] = $id;
            }
            $options = [
                'grid' => ($duplicate_matches[2] ?? '') === '-grid',
            ];
            $pages = [
                ['ids' => $ids],
            ];
            return [$pages, $options];
        }
        $pattern_param = $num_per_page - 1;
        $pattern = "/^((?:[0-9]+,){{$pattern_param}}[0-9]+)(-grid)?$/i";
        $list_res = preg_match($pattern, $spec, $list_matches);
        if ($list_res) {
            $ids = array_map(function ($idstr) {
                return intval($idstr);
            }, explode(',', $list_matches[1]));
            $options = [
                'grid' => ($list_matches[2] ?? '') === '-grid',
            ];
            $pages = [
                ['ids' => $ids],
            ];
            return [$pages, $options];
        }
        throw new NotFoundHttpException("Invalid spec: {$spec} ({$pattern})");
    }

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
        $is_mine = $owner && $current_user && ($owner->getId() === $current_user->getId());
        if (!$has_panini && !$is_mine) {
            throw new AccessDeniedHttpException("Kein Zugriff");
        }

        $wid = intval(round(($is_landscape ? self::PANINI_LONG : self::PANINI_SHORT)
        * self::DPI / self::MM_PER_INCH));
        $hei = intval(round(($is_landscape ? self::PANINI_SHORT : self::PANINI_LONG)
            * self::DPI / self::MM_PER_INCH));
        $suffix = "{$lp_suffix}_{$wid}x{$hei}";
        $payload_folder = (intval($id) >= 1000) ? "portraits/{$id}/" : '';
        $payload_path = "{$panini_path}{$payload_folder}{$img_src}";
        $bottom_mask_path = "{$masks_path}bottom{$suffix}.png";
        $top_mask_path = "{$masks_path}top{$suffix}.png";
        $association_mask_path = "{$masks_path}association{$suffix}.png";
        $flag_mask_path = "{$masks_path}associationStencil{$suffix}.png";
        $association_file = self::ASSOCIATION_MAP[$association] ?? null;
        $association_img_orig_path = "{$panini_path}{$association_file}";

        $ident = json_encode([
            $is_landscape,
            $has_top,
            $img_src,
            $img_style,
            $line1,
            $line2,
            $association,
            filemtime($payload_path),
            filemtime($bottom_mask_path),
            filemtime($top_mask_path),
            filemtime($association_mask_path),
            filemtime($flag_mask_path),
            filemtime($association_img_orig_path),
            md5(file_get_contents(__FILE__)),
        ]);
        $md5 = md5($ident);
        $cache_file = "{$panini_path}cache/{$id}-{$md5}.jpg";
        if (is_file($cache_file)) {
            $this->log()->info("Read from cache: {$id}-{$md5}.jpg");
            return file_get_contents($cache_file);
        }

        $img = imagecreatetruecolor($wid, $hei);
        gc_collect_cycles();

        // Payload
        $payload_img = imagecreatefromjpeg($payload_path);
        if ($payload_img) {
            $payload_wid = imagesx($payload_img);
            $payload_hei = imagesy($payload_img);
            imagecopyresampled(
                $img,
                $payload_img,
                intval(round($img_left_percent * $wid / 100)),
                intval(round($img_top_percent * $hei / 100)),
                0,
                0,
                intval(round($wid * $img_wid_percent / 100)),
                intval(round($wid * $img_wid_percent * $payload_hei / $payload_wid / 100)),
                $payload_wid,
                $payload_hei,
            );
            imagedestroy($payload_img);
            gc_collect_cycles();
        }

        // Masks
        $bottom_mask = imagecreatefrompng($bottom_mask_path);
        imagecopy($img, $bottom_mask, 0, 0, 0, 0, $wid, $hei);
        imagedestroy($bottom_mask);
        gc_collect_cycles();
        if ($has_top) {
            $top_mask = imagecreatefrompng($top_mask_path);
            imagecopy($img, $top_mask, 0, 0, 0, 0, $wid, $hei);
            imagedestroy($top_mask);
            gc_collect_cycles();
        }

        // Association
        if ($association_file) {
            $association_mask = imagecreatefrompng($association_mask_path);
            imagecopy($img, $association_mask, 0, 0, 0, 0, $wid, $hei);
            imagedestroy($association_mask);
            gc_collect_cycles();

            $offset = intval(round(($wid + $hei) * 0.01) - 1);
            $size = intval(round(($wid + $hei) * 0.09) + 1);

            $flag_mask = imagecreatefrompng($flag_mask_path);

            $association_img = imagecreatetruecolor($size, $size);
            $association_img_orig = imagecreatefromjpeg($association_img_orig_path);
            imagecopyresampled(
                $association_img,
                $association_img_orig,
                0,
                0,
                0,
                0,
                $size,
                $size,
                imagesx($association_img_orig),
                imagesy($association_img_orig),
            );
            imagedestroy($association_img_orig);
            gc_collect_cycles();

            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    $mask = imagecolorsforindex(
                        $flag_mask,
                        imagecolorat($flag_mask, $x + $offset, $y + $offset)
                    );
                    if ($mask['red'] > 0) {
                        $ratio = floatval($mask['red']) / 255.0;
                        $src = imagecolorsforindex(
                            $association_img,
                            imagecolorat($association_img, $x, $y)
                        );
                        $src_r = floatval($src['red']);
                        $src_g = floatval($src['green']);
                        $src_b = floatval($src['blue']);
                        $dst = imagecolorsforindex(
                            $img,
                            imagecolorat($img, $x + $offset, $y + $offset)
                        );
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
            imagedestroy($flag_mask);
            imagedestroy($association_img);
            gc_collect_cycles();
        }

        // Text
        $size = ($hei + ($is_landscape ? $wid : $hei)) * 0.018;
        $yellow = imagecolorallocate($img, 255, 255, 0);
        $shady = imagecolorallocatealpha($img, 0, 0, 0, 64);
        $shoff = $size / 10;
        $font_path = "{$panini_path}fonts/OpenSans/OpenSans-SemiBold.ttf";
        $box = imagettfbbox($size, 0, $font_path, $line1);
        $textwid = $box[2];
        $x = ($line2 ? $wid * 0.95 - $textwid : $wid / 2 - $textwid / 2);
        $y = $hei * ($is_landscape ? 0.95 : ($line2 ? 0.915 : 0.945));
        $this->drawText($img, $size, 0, $x + $shoff, $y + $shoff, $shady, $font_path, $line1);
        $this->drawText($img, $size, 0, $x, $y, $yellow, $font_path, $line1);
        if ($line2) {
            $box = imagettfbbox($size, 0, $font_path, $line2);
            $textwid = $box[2];
            $x = $wid * 0.95 - $textwid;
            $y = $hei * 0.975;
            $this->drawText($img, $size, 0, $x + $shoff, $y + $shoff, $shady, $font_path, $line2);
            $this->drawText($img, $size, 0, $x, $y, $yellow, $font_path, $line2);
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

    public function render3x5Pages($pages, $options) {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
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
                $this->cachePictureId($id);
            }
        }

        $pdf = new \TCPDF('P', 'mm', 'A4');
        $pdf->setAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
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
                        $temp_file_path = $this->getCachePathForPictureId($id);
                        if ($temp_file_path) {
                            $pdf->Image(
                                $temp_file_path,
                                $x_offset + $x_margin + $x_step * $x,
                                $y_offset + $y_margin + $y_step * $y,
                                $x_step - $x_margin * 2,
                            );
                        }
                        $index++;
                    }
                }
            }
        }
        return $pdf->Output('3x5.pdf', 'S');
    }

    public function render4x4Zip($options): string {
        $grid_or_empty = $options['grid'] ? '-grid' : '';
        $ids = $this->getAllEntries();

        $panini_utils = Panini2024Utils::fromEnv();
        $zip = new \ZipArchive();
        $zip_path = $panini_utils->getCachePathForZip("duplicates{$grid_or_empty}");
        if ($zip->open($zip_path, \ZipArchive::CREATE) !== true) {
            throw new \Exception("Could not open Zip");
        }
        foreach ($ids as $id) {
            $spec = "duplicate-{$id}{$grid_or_empty}";
            $pdf_out = null;
            [$pages, $options] = $this->parseSpec($spec, /* num_per_page= */ 16);
            $pdf_out = $panini_utils->render4x4Pages($pages, $options);
            if (!$pdf_out) {
                throw new \Exception("PDF generation failed for ID: {$id}");
            }
            $zip->addFromString("{$spec}.pdf", $pdf_out);
            gc_collect_cycles();
        }
        $zip->close();

        $content = file_get_contents($zip_path);
        @unlink($zip_path);
        return $content;
    }

    private function getAllEntries() {
        $ids = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT id FROM panini24 ORDER BY id ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $ids[] = $row_olz['id'];
        }
        return $ids;
    }

    public function render4x4Pages($pages, $options): string {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }

        $grid = (bool) ($options['grid'] ?? false);
        $x_step = 48;
        $x_margin = 2;
        $x_offset = 9;
        $y_step = 68;
        $y_offset = 12.5;
        $y_margin = 2;

        foreach ($pages as $page) {
            $ids = $page['ids'] ?? [];
            foreach (array_unique($ids) as $id) {
                $this->cachePictureId($id);
            }
        }

        $pdf = new \TCPDF('P', 'mm', 'A4');
        $pdf->setAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        foreach ($pages as $page) {
            $ids = $page['ids'] ?? [];
            $pdf->AddPage();

            if ($grid) {
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.1);
                for ($x = 0; $x < 5; $x++) {
                    $line_x = $x_offset + $x_step * $x;
                    $pdf->Line($line_x, 0, $line_x, $y_offset - 1);
                    $pdf->Line($line_x, 297, $line_x, 297 - $y_offset + 1);
                }
                for ($y = 0; $y < 5; $y++) {
                    $line_y = $y_offset + $y_step * $y;
                    $pdf->Line(0, $line_y, $x_offset - 1, $line_y);
                    $pdf->Line(210, $line_y, 210 - $x_offset + 1, $line_y);
                }
            }

            $index = 0;
            for ($y = 0; $y < 4; $y++) {
                for ($x = 0; $x < 4; $x++) {
                    $id = $ids[$index] ?? 0;
                    $temp_file_path = $this->getCachePathForPictureId($id);
                    if ($temp_file_path) {
                        $pdf->Image(
                            $temp_file_path,
                            $x_offset + $x_margin + $x_step * $x,
                            $y_offset + $y_margin + $y_step * $y,
                            $x_step - $x_margin * 2,
                        );
                    }
                    $index++;
                }
            }
        }
        return $pdf->Output('4x4.pdf', 'S');
    }

    private function cachePictureId($id) {
        if ($id === 0) {
            return;
        }
        $temp_file_path = $this->getCachePathForPictureId($id);
        $img = imagecreatefromstring($this->renderSingle($id));
        $wid = imagesx($img);
        $hei = imagesy($img);
        if ($hei < $wid) {
            $img = imagerotate($img, 90, 0);
        }
        imagejpeg($img, $temp_file_path);
        imagedestroy($img);
        gc_collect_cycles();
    }

    private function getCachePathForPictureId($id) {
        if ($id === 0) {
            return null;
        }
        $data_path = $this->envUtils()->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0o777, true);
        }
        return "{$temp_path}paninipdf-{$id}.jpg";
    }

    public function getCachePathForZip($ident) {
        $data_path = $this->envUtils()->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0o777, true);
        }
        return "{$temp_path}paninizip-{$ident}.zip";
    }

    // --- BOOK ------------------------------------------------------------------------------------

    private function getBookPdf(): \TCPDF {
        $data_path = $this->envUtils()->getDataPath();
        $panini_path = "{$data_path}panini_data/";

        $pdf = new \TCPDF('P', 'mm', 'A4');
        $pdf->setAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $font_path = "{$panini_path}fonts/OpenSans/OpenSans-SemiBold.ttf";
        $fontname = \TCPDF_FONTS::addTTFfont($font_path, 'TrueTypeUnicode');
        $pdf->SetFont($fontname);

        return $pdf;
    }

    private function addBookPage(\TCPDF $pdf) {
        $pdf->AddPage();
        $mainbg_path = __DIR__.'/../../../../assets/icns/mainbg.png';
        $info = getimagesize($mainbg_path);
        $wid_px = $info[0];
        $hei_px = $info[1];
        $dpi = 150;
        $wid_mm = $wid_px / $dpi * self::MM_PER_INCH;
        $hei_mm = $hei_px / $dpi * self::MM_PER_INCH;
        for ($tile_x = 0; $tile_x < ceil(210 / $wid_mm); $tile_x++) {
            for ($tile_y = 0; $tile_y < ceil(297 / $hei_mm); $tile_y++) {
                $pdf->Image(
                    $mainbg_path,
                    $tile_x * $wid_mm,
                    $tile_y * $hei_mm,
                    $wid_mm,
                );
            }
        }
    }

    private function drawPlaceholder(\TCPDF $pdf, $entry, $x, $y, $wid, $hei) {
        $is_landcape = $wid > $hei;

        $pdf->SetLineWidth(0.1);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($x, $y, $wid, $hei, 'DF');

        $pdf->SetTextColor(200, 200, 200);
        $pdf->SetFontSize($is_landcape ? 7.75 : 8.75);
        $line1 = $this->convertString($entry['line1']);
        $line2 = $this->convertString($entry['line2']);
        $has_line2 = $line2 !== '';
        $align = $has_line2 ? 'R' : 'C';
        $pdf->setXY($x, $y + $hei - ($has_line2 ? 10 : 8));
        $pdf->Cell($wid, 5, $line1, 0, 0, $align);
        if ($has_line2) {
            $pdf->setXY($x, $y + $hei - 6);
            $pdf->Cell($wid, 5, $line2, 0, 0, $align);
        }
    }

    private function drawEntryInfobox(\TCPDF $pdf, $entry, $x, $y, $wid, $hei) {
        $pdf->SetLineWidth(0.1);
        $pdf->SetDrawColor(0, 117, 33);
        $pdf->SetFillColor(212, 231, 206);
        $pdf->Rect($x, $y, $wid, $hei, 'F');
        $line_y = $y;
        $pdf->Line($x, $line_y, $x + $wid, $line_y);
        $line_y = $y + $hei;
        $pdf->Line($x, $line_y, $x + $wid, $line_y);

        $pdf->SetFontSize(11);
        $birthday = $entry['birthdate'] ?? '';
        if (substr($birthday, 4) === '-00-00') {
            $birthday = substr($birthday, 0, 4);
        }
        if ($entry['birthdate'] === null) { // No information => ERROR!
            $pdf->SetTextColor(255, 0, 0);
            $birthday = '!!!';
        } elseif (substr($birthday, 0, 4) === '0000') { // Year Zero => Do not show
            $birthday = '';
        } else {
            $pdf->SetTextColor(0, 117, 33);
            if (strlen($birthday) === 10) {
                $birthday = date('d.m.Y', strtotime($birthday));
            }
        }
        $pdf->setXY($x, $y + 1);
        $pdf->Cell($wid * 0.7 - 2, 5, $birthday);

        $pdf->SetFontSize(14);
        $num_mispunch = strval($entry['num_mispunches']);
        if ($entry['num_mispunches'] === null) { // No information => ERROR!
            $pdf->SetTextColor(255, 0, 0);
            $num_mispunch = '!!!';
        } elseif (intval($entry['num_mispunches']) < 0) { // Negative count => Do not show
            $num_mispunch = '';
        } else {
            $pdf->SetTextColor(0, 117, 33);
        }
        $pdf->setXY($x + $wid * 0.7, $y + 1);
        $pdf->Cell($wid * 0.3, 5, $num_mispunch, 0, 0, 'R');

        $pdf->SetTextColor(0, 117, 33);
        $pdf->SetFontSize(8);
        $infos = json_decode($entry['infos'], true) ?? [];
        $favourite_map = $this->convertString($infos[0] ?? '');
        $pdf->setXY($x, $y + 6);
        $pdf->Cell($wid, 4, $favourite_map);
        $since_when = $this->convertString($infos[3] ?? '');
        $pdf->setXY($x, $y + 10);
        $pdf->Cell($wid, 4, $since_when);
        $motto = $this->convertString($infos[4] ?? '');
        $pdf->setXY($x, $y + 14);
        $pdf->Multicell($wid, 11, $motto, 0, 'L');
    }

    private function drawText(
        \GdImage|int $image,
        float $size,
        float $angle,
        int|float $x,
        int|float $y,
        int $color,
        string $font_filename,
        string $text,
        array $options = [],
    ) {
        $x = intval(round($x));
        $y = intval(round($y));
        return imagettftext($image, $size, $angle, $x, $y, $color, $font_filename, $text, $options);
    }

    private function convertString($string) {
        return $string ?? '';
    }

    public function renderBookPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getBookEntries();

        $x_step = 46;
        $x_margin = 1;
        $x_offset = 13;
        $y_step = 92;
        $y_offset = 10.5;
        $y_margin = 1;
        $y_box = 26;

        $index = 0;
        foreach ($entries as $entry) {
            if (($index % 12) === 0) {
                $this->addBookPage($pdf);
            }
            $x_index = $index % 4;
            $y_index = floor($index / 4) % 3;

            $x = $x_offset + $x_margin + $x_step * $x_index;
            $y = $y_offset + $y_margin + $y_step * $y_index;
            $wid = $x_step - $x_margin * 2;

            $placeholder_hei = $y_step - $y_box - $y_margin * 2;
            $this->drawPlaceholder($pdf, $entry, $x, $y, $wid, $placeholder_hei);

            $box_y = $y + $y_step - $y_box - $y_margin;
            $box_hei = $y_box - $y_margin;
            $this->drawEntryInfobox($pdf, $entry, $x, $box_y, $wid, $box_hei);

            $index++;
        }
        return $pdf->Output('book.pdf', 'S');
    }

    private function getBookEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_associations = $db->query("SELECT *, (img_src = 'wappen/other.jpg') AS is_other FROM panini24 WHERE img_src LIKE 'wappen/%' ORDER BY is_other ASC, line1 ASC");
        $esc_associations = [];
        for ($i = 0; $i < $result_associations->num_rows; $i++) {
            $row_association = $result_associations->fetch_assoc();
            $entries[] = $row_association;

            if ($row_association['is_other']) {
                $sql = implode("', '", $esc_associations);
                $result_portraits = $db->query("SELECT * FROM panini24 WHERE association NOT IN ('{$sql}') ORDER BY line2 ASC, line1 ASC");
                for ($j = 0; $j < $result_portraits->num_rows; $j++) {
                    $row_portrait = $result_portraits->fetch_assoc();
                    $entries[] = $row_portrait;
                }
            } else {
                $esc_association = $db->real_escape_string($row_association['line1']);
                $esc_associations[] = $esc_association;
                $result_portraits = $db->query("SELECT * FROM panini24 WHERE association = '{$esc_association}' ORDER BY line2 ASC, line1 ASC");
                for ($j = 0; $j < $result_portraits->num_rows; $j++) {
                    $row_portrait = $result_portraits->fetch_assoc();
                    $entries[] = $row_portrait;
                }
            }
        }
        return $entries;
    }

    public function renderOlzPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getOlzEntries();

        $index = 0;
        $last_page = 0;
        foreach ($entries as $entry) {
            [$page, $x, $y] = $this->getOlzPageXY($index);
            for ($p = $last_page; $p < $page; $p++) {
                $this->addBookPage($pdf);
            }
            $last_page = $page;

            $short = 44;
            $long = 64;
            $wid = $entry['is_landscape'] ? $long : $short;
            $hei = $entry['is_landscape'] ? $short : $long;

            $this->drawPlaceholder($pdf, $entry, $x - $wid / 2, $y - $hei / 2, $wid, $hei);

            $index++;
        }
        return $pdf->Output('olz.pdf', 'S');
    }

    private function getOlzEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT * FROM panini24 WHERE id >= 10 AND id < 20 ORDER BY id ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $entries[] = $row_olz;
        }
        return $entries;
    }

    private function getOlzPageXY(int $index): array {
        $a4_wid = 210;
        $olz = [
            [1, $a4_wid * 0.75, 10.5 + 22],
            [1, $a4_wid * 0.25, 10.5 + 44 + 10.5 + 32],
            [1, $a4_wid * 0.8, 10.5 + 44 + 10.5 + 32],
            [1, $a4_wid * 0.2, 10.5 + 44 + 10.5 + 64 + 10.5 + 32],
            [2, $a4_wid * 0.8, 10.5 + 44 + 10.5 + 64 + 10.5 + 64 + 10.5 + 32],
            [2, $a4_wid * 0.25, 10.5 + 22],
            [2, $a4_wid * 0.75, 10.5 + 44 + 10.5 + 32],
            [2, $a4_wid * 0.2, 10.5 + 44 + 10.5 + 64 + 10.5 + 32],
            [2, $a4_wid * 0.25, 10.5 + 44 + 10.5 + 64 + 10.5 + 64 + 10.5 + 32],
        ];
        return $olz[$index];
    }

    public function renderHistoryPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getHistoryEntries();

        $index = 0;
        $last_page = 0;
        foreach ($entries as $entry) {
            [$page, $x, $y] = $this->getHistoryPageXY($index);
            for ($p = $last_page; $p < $page; $p++) {
                $this->addBookPage($pdf);
            }
            $last_page = $page;

            $short = 44;
            $long = 64;
            $wid = $entry['is_landscape'] ? $long : $short;
            $hei = $entry['is_landscape'] ? $short : $long;

            $this->drawPlaceholder($pdf, $entry, $x - $wid / 2, $y - $hei / 2, $wid, $hei);

            $index++;
        }
        return $pdf->Output('history.pdf', 'S');
    }

    private function getHistoryEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT * FROM panini24 WHERE id >= 50 AND id < 100 ORDER BY id ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $entries[] = $row_olz;
        }
        return $entries;
    }

    private function getHistoryPageXY(int $index): array {
        $a4_wid = 210;
        $olz = [
            [1, $a4_wid * 0.75, 5.5 + 32 * 1],
            [1, $a4_wid * 0.25, 5.5 + 32 * 2],
            [1, $a4_wid * 0.75, 5.5 + 32 * 3],
            [1, $a4_wid * 0.25, 5.5 + 32 * 4],
            [1, $a4_wid * 0.75, 5.5 + 32 * 5],
            [1, $a4_wid * 0.25, 5.5 + 32 * 6],
            [1, $a4_wid * 0.75, 5.5 + 32 * 7],
            [1, $a4_wid * 0.25, 5.5 + 32 * 8],
            [2, $a4_wid * 0.75, 5.5 + 32 * 8],
            [2, $a4_wid * 0.25, 5.5 + 32 * 7],
            [2, $a4_wid * 0.75, 5.5 + 32 * 6],
            [2, $a4_wid * 0.25, 5.5 + 32 * 5],
            [2, $a4_wid * 0.75, 5.5 + 32 * 4],
            [2, $a4_wid * 0.25, 5.5 + 32 * 3],
            [2, $a4_wid * 0.75, 5.5 + 32 * 2],
            [2, $a4_wid * 0.25, 5.5 + 32 * 1],
        ];
        return $olz[$index];
    }

    public function renderDressesPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getDressesEntries();

        $index = 0;
        $last_page = 0;
        foreach ($entries as $entry) {
            [$page, $x, $y] = $this->getDressesPageXY($index);
            for ($p = $last_page; $p < $page; $p++) {
                $this->addBookPage($pdf);
            }
            $last_page = $page;

            $short = 44;
            $long = 64;
            $wid = $entry['is_landscape'] ? $long : $short;
            $hei = $entry['is_landscape'] ? $short : $long;

            $this->drawPlaceholder($pdf, $entry, $x - $wid / 2, $y - $hei / 2, $wid, $hei);

            $index++;
        }
        return $pdf->Output('dresses.pdf', 'S');
    }

    private function getDressesEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT * FROM panini24 WHERE id >= 40 AND id < 50 ORDER BY id ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $entries[] = $row_olz;
        }
        return $entries;
    }

    private function getDressesPageXY(int $index): array {
        $a4_wid = 210;
        $olz = [
            [1, $a4_wid * 0.25, 42 + 22],
            [1, $a4_wid * 0.75, 42 + 44 - 6 + 22],
            [1, $a4_wid * 0.25, 42 + 44 - 6 + 44 - 6 + 22],
            [1, $a4_wid * 0.75, 42 + 44 - 6 + 44 - 6 + 44 - 6 + 22],
            [1, $a4_wid * 0.25, 42 + 44 - 6 + 44 - 6 + 44 - 6 + 44 - 6 + 22],
            [1, $a4_wid * 0.75, 42 + 44 - 6 + 44 - 6 + 44 - 6 + 44 - 6 + 44 - 6 + 22],
        ];
        return $olz[$index];
    }

    public function renderMapsPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getMapsEntries();

        $index = 0;
        $last_page = 0;
        foreach ($entries as $entry) {
            [$page, $x, $y] = $this->getMapsPageXY($index);
            for ($p = $last_page; $p < $page; $p++) {
                $this->addBookPage($pdf);
            }
            $last_page = $page;

            $short = 44;
            $long = 64;
            $wid = $entry['is_landscape'] ? $long : $short;
            $hei = $entry['is_landscape'] ? $short : $long;

            $this->drawPlaceholder($pdf, $entry, $x - $wid / 2, $y - $hei / 2, $wid, $hei);

            $this->drawEntryInfobox($pdf, $entry, $x - $wid / 2, $y + $hei / 2 + 1, $wid, 25);

            $index++;
        }
        return $pdf->Output('maps.pdf', 'S');
    }

    private function getMapsEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT * FROM panini24 WHERE id >= 100 AND id < 150 ORDER BY line1 ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $entries[] = $row_olz;
        }
        return $entries;
    }

    private function getMapsPageXY(int $index): array {
        $x_step = 46;
        $x_offset = 13;
        $y_step = 92;
        $y_offset = 10.5;
        $y_box = 26;

        $col1 = $x_offset + $x_step * 1 / 2;
        $col2 = $x_offset + $x_step * 3 / 2;
        $col3 = $x_offset + $x_step * 5 / 2;
        $col4 = $x_offset + $x_step * 7 / 2;

        $row1 = $y_offset + $y_step * 1 / 2 - $y_box / 2;
        $row2 = $y_offset + $y_step * 3 / 2 - $y_box / 2;
        $row3 = $y_offset + $y_step * 5 / 2 - $y_box / 2;

        $olz = [
            [1, $col1, $row1],
            // [1, $col2, $row1],
            [1, $col3, $row1],
            [1, $col4, $row1],
            [1, $col1, $row2],
            // [1, $col2, $row2],
            // [1, $col3, $row2],
            [1, $col4, $row2],
            [1, $col1, $row3],
            [1, $col2, $row3],
            [1, $col3, $row3],
            [1, $col4, $row3],
            [2, $col1, $row1],
            [2, $col2, $row1],
            [2, $col3, $row1],
            [2, $col4, $row1],
            [2, $col1, $row2],
            [2, $col2, $row2],
            [2, $col3, $row2],
            [2, $col4, $row2],
            [2, $col1, $row3],
            [2, $col2, $row3],
            [2, $col3, $row3],
            [2, $col4, $row3],
        ];
        return $olz[$index];
    }

    public function renderBackPages() {
        if (!$this->authUtils()->hasPermission('panini2024')) {
            throw new NotFoundHttpException();
        }
        $pdf = $this->getBookPdf();
        $entries = $this->getBackEntries();

        $index = 0;
        $last_page = 0;
        foreach ($entries as $entry) {
            [$page, $x, $y] = $this->getBackPageXY($index);
            for ($p = $last_page; $p < $page; $p++) {
                $this->addBookPage($pdf);
            }
            $last_page = $page;

            $short = 44;
            $long = 64;
            $wid = $entry['is_landscape'] ? $long : $short;
            $hei = $entry['is_landscape'] ? $short : $long;

            $this->drawPlaceholder($pdf, $entry, $x - $wid / 2, $y - $hei / 2, $wid, $hei);

            $index++;
        }
        return $pdf->Output('back.pdf', 'S');
    }

    private function getBackEntries() {
        $entries = [];

        $db = $this->dbUtils()->getDb();
        $result_olz = $db->query("SELECT * FROM panini24 WHERE id >= 20 AND id < 40 ORDER BY id ASC");
        for ($i = 0; $i < $result_olz->num_rows; $i++) {
            $row_olz = $result_olz->fetch_assoc();
            $entries[] = $row_olz;
        }
        return $entries;
    }

    private function getBackPageXY(int $index): array {
        $a4_wid = 210;
        $olz = [
            [1, $a4_wid * 0.75, 297 - 10.5 - 22],
            [2, $a4_wid * 0.75, 297 - 10.5 - 22],
        ];
        return $olz[$index];
    }

    public static function fromEnv(): self {
        return new self();
    }
}
