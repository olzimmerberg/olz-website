<?php

namespace Olz\Suche\Controller;

use Olz\Common\Utils\HttpUtils;
use Olz\Suche\Components\OlzSuche\OlzSuche;
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
        return $httpUtils->measure($request, [], function () use ($olzSuche) {
            $out = $olzSuche->getHtml([]);
            return new Response($out);
        });
    }
}
