<?php

namespace Olz\Controller;

use Olz\Service\Components\OlzService\OlzService;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController {
    #[Route('/service')]
    public function service(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzService $olzService,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzService->getHtml([]);
        return new Response($out);
    }
}
