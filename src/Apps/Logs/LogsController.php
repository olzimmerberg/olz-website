<?php

namespace Olz\Apps\Logs;

use Olz\Apps\Logs\Components\OlzLogs\OlzLogs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends AbstractController {
    #[Route('/apps/logs')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzLogs::render();
        return new Response($html_out);
    }
}
