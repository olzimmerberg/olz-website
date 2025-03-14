<?php

namespace Olz\Controller;

use Olz\Suche\Components\OlzSuche\OlzSuche;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SucheController extends AbstractController {
    #[Route('/suche')]
    public function suche(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzSuche $olzSuche,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzSuche->getHtml([]);
        return new Response($out);
    }
}
