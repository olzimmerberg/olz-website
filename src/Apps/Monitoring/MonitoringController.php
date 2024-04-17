<?php

namespace Olz\Apps\Monitoring;

use Olz\Apps\Monitoring\Components\OlzMonitoring\OlzMonitoring;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonitoringController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/monitoring')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzMonitoring::render([]);
        return new Response($html_out);
    }
}
