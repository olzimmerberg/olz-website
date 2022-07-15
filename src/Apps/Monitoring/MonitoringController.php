<?php

namespace Olz\Apps\Monitoring;

use Olz\Apps\Monitoring\Components\OlzMonitoring\OlzMonitoring;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonitoringController extends AbstractController {
    #[Route('/apps/monitoring')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzMonitoring::render([]);
        return new Response($html_out);
    }
}
