<?php

namespace Olz\Controller;

use Olz\Anniversary\Components\OlzAnniversary\OlzAnniversary;
use Olz\Anniversary\Components\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnniversaryController extends AbstractController {
    #[Route('/2026')]
    public function anniversary(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzAnniversary $olzAnniversary,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzAnniversary->getHtml([]);
        return new Response($out);
    }

    #[Route('/2026/rakete.svg')]
    public function rakete(
        OlzAnniversaryRocket $olzAnniversaryRocket,
    ): Response {
        $out = $olzAnniversaryRocket->getHtml([]);
        return new Response($out);
    }
}
