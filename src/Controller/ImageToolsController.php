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

    // This is a backup mechanism for the case where the thumbnail does not exist yet
    #[Route('/img/{db_table}/{id}/thumb/{index}${dimension}.jpg', requirements: [
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
        $entity_img_path = "{$data_path}{$db_imgpath}{$id}/";
        $imgfile = "{$entity_img_path}img/{$index}";
        if (!is_file($imgfile)) {
            throw new NotFoundHttpException("No such image: {$imgfile}");
        }
        $dim = $this->imageUtils()->getThumbSize($dimension);
        if ($dim < 32) {
            $dim = 32;
        }
        $thumbfile = '';
        try {
            $thumbfile = $this->imageUtils()->getThumbFile($index, $entity_img_path, $dim);
        } catch (\Throwable $th) {
            throw new BadRequestHttpException($th->getMessage());
        }
        $filemtime = @filemtime($thumbfile);
        $one_second_ago = time() - 1;
        if ($filemtime > $one_second_ago) {
            $this->log()->notice("Remaining thumb: {$thumbfile}");
        }
        $response = new Response(file_get_contents($thumbfile) ?: null);
        $response->headers->set('Cache-Control', 'max-age=2592000');
        $response->headers->set('Content-Type', 'image/jpeg');
        return $response;
    }
}
