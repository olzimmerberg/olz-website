<?php

namespace Olz\Controller;

use Olz\Service\Components\OlzService\OlzService;
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
    ): Response {
        $out = OlzService::render();
        return new Response($out);
    }
}
