<?php

namespace Olz\Apps\Monitoring;

use Olz\Apps\Monitoring\Components\OlzMonitoring\OlzMonitoring;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonitoringController extends AbstractController {
    #[Route('/apps/monitoring')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzMonitoring $olzMonitoring,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzMonitoring->getHtml([]);
        return new Response($html_out);
    }
}
