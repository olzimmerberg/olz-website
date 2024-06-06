<?php

namespace Olz\Controller;

use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RobotsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/robots.txt')]
    public function index(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = <<<'ZZZZZZZZZZ'
            User-agent: *
            Disallow: /apps/
            Disallow: /downloads/
            Disallow: /files/
            Disallow: /img/
            Allow: /img/fuer_einsteiger/
            Disallow: /pdf/
            Disallow: /olz_mitglieder/
            Disallow: /trainingphotos/
            User-agent: Googlebot-Image
            Disallow: /
            Allow: /assets/
            Allow: /favicon.ico
            Allow: /img/fuer_einsteiger/

            Sitemap: https://olzimmerberg.ch/sitemap.xml
            ZZZZZZZZZZ;
        $response = new Response($out);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
