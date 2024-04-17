<?php

namespace Olz\Apps\Logs;

use Olz\Apps\Logs\Components\OlzLogs\OlzLogs;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/logs')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzLogs::render();
        return new Response($html_out);
    }
}
