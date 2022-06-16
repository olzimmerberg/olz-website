<?php

namespace Olz\Controller;

use Olz\Components\OlzSitemap\OlzSitemap;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController {
    #[Route('/sitemap.xml')]
    public function get(
        LoggerInterface $logger,
    ): Response {
        $response = new Response(OlzSitemap::render());
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }
}
