<?php

namespace Olz\Apps\Logs;

use Olz\Apps\Logs\Components\OlzLogs\OlzLogs;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends AbstractController {
    #[Route('/apps/logs')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzLogs $olzLogs,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzLogs->getHtml([]);
        return new Response($html_out);
    }
}
