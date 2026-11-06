<?php

namespace Olz\Startseite\Controller;

use Olz\Common\Utils\HttpUtils;
use Olz\Startseite\Components\OlzStartseite\OlzStartseite;
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
        return $httpUtils->measure($request, ['von'], function () use ($httpUtils, $request, $olzStartseite) {
            $httpUtils->stripParams($request, ['von']);
            $out = $olzStartseite->getHtml([]);
            return new Response($out);
        });
    }
}
