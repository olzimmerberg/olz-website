<?php

namespace Olz\Apps\Oev;

use Olz\Apps\Oev\Components\OlzOev\OlzOev;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OevController extends AbstractController {
    #[Route('/apps/oev')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzOev $olzOev,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzOev->getHtml([]);
        return new Response($html_out);
    }
}
