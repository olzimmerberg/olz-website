<?php

namespace Olz\Controller;

use Olz\Utils\ImageUtils;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ImageToolsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/image_tools/thumb/{db_table}${id}${index}${dimension}.jpg', requirements: [
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
        $data_path = $this->envUtils()->getDataPath();

        session_write_close();
        if (!isset(ImageUtils::TABLES_IMG_DIRS[$db_table])) {
            throw new NotFoundHttpException("No such DB table: {$db_table}");
        }
        $db_imgpath = ImageUtils::TABLES_IMG_DIRS[$db_table];
        $imgfile = "{$data_path}{$db_imgpath}/{$id}/img/{$index}";
        if (!is_file($imgfile)) {
            throw new NotFoundHttpException("No such image: {$imgfile}");
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
            throw new NotFoundHttpException("Invalid dimension: {$dimension}");
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
            if (!$img) {
                $message = "Could not open image {$imgfile}";
                $this->log()->warning($message);
                throw new BadRequestHttpException($message);
            }
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
