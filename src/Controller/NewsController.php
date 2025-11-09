<?php

namespace Olz\Controller;

use Olz\Entity\News\NewsEntry;
use Olz\News\Components\OlzNewsDetail\OlzNewsDetail;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Olz\Utils\HttpUtils;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/news')]
    public function newsList(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzNewsList $olzNewsList,
    ): Response {
        $httpUtils->countRequest($request, ['filter', 'von']);
        $httpUtils->stripParams($request, ['von']);
        $out = $olzNewsList->getHtml([]);
        return new Response($out);
    }

    #[Route('/news/{id}', requirements: ['id' => '\d+'])]
    public function newsDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzNewsDetail $olzNewsDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $httpUtils->stripParams($request, ['von']);
        $out = $olzNewsDetail->getHtml(['id' => $id]);
        return new Response($out);
    }

    #[Route('/news/{id}/all.zip', requirements: ['id' => '\d+'])]
    public function all(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        int $id,
    ): Response {
        $httpUtils->countRequest($request);
        if (!$this->authUtils()->hasPermission('any')) {
            throw new NotFoundHttpException();
        }

        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $id]);
        if (!$news_entry) {
            throw new NotFoundHttpException();
        }
        $image_ids = $news_entry->getImageIds();

        $data_path = $this->envUtils()->getDataPath();
        $imgdir = "{$data_path}img/news/{$id}/img/";
        if (!is_dir($imgdir)) {
            throw new NotFoundHttpException("No such image directory: {$imgdir}");
        }

        $this->log()->info("Downloading all images zip file for news/{$id}");
        $zip_id = $this->uploadUtils()->getRandomUploadId('.zip');
        $zip_path = "{$data_path}temp/{$zip_id}";
        $zip = new \ZipArchive();
        if ($zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->log()->error("Could not create ZIP file: {$zip_path}");
            throw new \Exception("Could not create ZIP file: {$zip_path}");
        }
        $num_images = count($image_ids);
        for ($i = 0; $i < $num_images; $i++) {
            $image_id = $image_ids[$i];
            $file_path = "{$imgdir}{$image_id}";
            if (is_file($file_path)) {
                $index = $i + 1;
                $pad_len = intval(ceil(log10($num_images)));
                $name = str_pad("{$index}", $pad_len, '0', STR_PAD_LEFT);
                $zip->addFile($file_path, "{$name}.jpg");
            } else {
                $this->log()->warning("Missing image in news/{$id}: {$file_path}");
            }
        }
        if ($zip->status != \ZipArchive::ER_OK) {
            $this->log()->error("Could write files to ZIP: {$imgdir}*.jpg");
            throw new \Exception("Could write files to ZIP: {$imgdir}*.jpg");
        }
        $zip->close();

        $response = new Response(file_get_contents($zip_path) ?: null);
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', "attachment;filename=Fotos {$news_entry->getTitle()}.zip");
        return $response;
    }
}
