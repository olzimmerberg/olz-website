<?php

namespace Olz\Apps\Commands;

use Olz\Apps\Commands\Components\OlzCommands\OlzCommands;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/commands')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzCommands::render();
        return new Response($html_out);
    }
}
