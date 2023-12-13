<?php

namespace Olz\Controller;

use Olz\Components\OlzHtmlSitemap\OlzHtmlSitemap;
use Olz\Components\OlzXmlSitemap\OlzXmlSitemap;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController {
    #[Route('/sitemap.xml')]
    public function xmlSitemap(
        LoggerInterface $logger,
    ): Response {
        $response = new Response(OlzXmlSitemap::render());
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    #[Route('/sitemap')]
    public function sitemap(
        LoggerInterface $logger,
    ): Response {
        return new Response(OlzHtmlSitemap::render());
    }
}
