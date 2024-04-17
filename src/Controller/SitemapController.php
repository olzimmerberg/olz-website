<?php

namespace Olz\Controller;

use Olz\Components\OlzHtmlSitemap\OlzHtmlSitemap;
use Olz\Components\OlzXmlSitemap\OlzXmlSitemap;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/sitemap.xml')]
    public function xmlSitemap(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $response = new Response(OlzXmlSitemap::render());
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    #[Route('/sitemap')]
    public function sitemap(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        return new Response(OlzHtmlSitemap::render());
    }
}
