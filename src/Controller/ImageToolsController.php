<?php

namespace Olz\Controller;

use Olz\Utils\EnvUtils;
use Olz\Utils\ImageUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageToolsController extends AbstractController {
    #[Route('/image_tools/thumb/{db_table}__{id}__{index}__{dimension}.jpg', requirements: [
        'db_table' => '[a-z_]+',
        'id' => '\d+',
        'dimension' => '\d+',
    ])]
    public function thumb(
        Request $request,
        LoggerInterface $logger,
        string $db_table,
        int $id,
        string $index,
        int $dimension,
    ): Response {
        $data_path = EnvUtils::fromEnv()->getDataPath();

        session_write_close();
        if (!isset(ImageUtils::TABLES_IMG_DIRS[$db_table])) {
            throw $this->createNotFoundException("No such DB table: {$db_table}");
        }
        $db_imgpath = ImageUtils::TABLES_IMG_DIRS[$db_table];
        $imgfile = "{$data_path}{$db_imgpath}/{$id}/img/{$index}";
        if (!is_file($imgfile)) {
            throw $this->createNotFoundException("No such image: {$imgfile}");
        }
        $ndim = $dimension - 1;
        $dim = false;
        for ($i = 1; $i < 9 && ($ndim >> $i) > 0; $i++) {
        }
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
            throw $this->createNotFoundException("Invalid dimension: {$dimension}");
        }
        if ($wid > 256 || $hei > 256) {
            $thumbfile = $imgfile;
        } else {
            $thumbfile = $data_path.$db_imgpath."/".$id."/thumb/".$index."_".$wid."x".$hei.".jpg";
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
        $response = new Response(file_get_contents($thumbfile));
        $response->headers->set('Cache-Control', 'max-age=2592000');
        $response->headers->set('Content-Type', 'image/jpeg');
        return $response;
    }
}
