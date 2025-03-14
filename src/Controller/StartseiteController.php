<?php

namespace Olz\Controller;

use Olz\Startseite\Components\OlzStartseite\OlzStartseite;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartseiteController extends AbstractController {
    #[Route('/')]
    public function startseite(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzStartseite $olzStartseite,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzStartseite->getHtml([]);
        return new Response($out);
    }
}
