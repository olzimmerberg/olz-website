<?php

namespace Olz\Controller;

use Olz\Components\OlzHtmlSitemap\OlzHtmlSitemap;
use Olz\Components\OlzXmlSitemap\OlzXmlSitemap;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController {
    #[Route('/sitemap.xml')]
    public function xmlSitemap(
        Request $request,
        LoggerInterface $logger,
        OlzXmlSitemap $olzXmlSitemap,
    ): Response {
        $response = new Response($olzXmlSitemap->getHtml([]));
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    #[Route('/sitemap')]
    public function sitemap(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzHtmlSitemap $olzHtmlSitemap,
    ): Response {
        $httpUtils->countRequest($request);
        return new Response($olzHtmlSitemap->getHtml([]));
    }
}
