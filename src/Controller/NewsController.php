<?php

namespace Olz\Controller;

use Olz\News\Components\OlzNewsDetail\OlzNewsDetail;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController {
    #[Route('/news')]
    public function newsList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzNewsList::render([]);
        return new Response($out);
    }

    #[Route('/news/{id}', requirements: ['id' => '\d+'])]
    public function newsDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = OlzNewsDetail::render(['id' => $id]);
        return new Response($out);
    }
}
