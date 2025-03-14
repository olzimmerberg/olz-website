<?php

namespace Olz\Apps\Commands;

use Olz\Apps\Commands\Components\OlzCommands\OlzCommands;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandsController extends AbstractController {
    #[Route('/apps/commands')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzCommands $olzCommands,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzCommands->getHtml([]);
        return new Response($html_out);
    }
}
