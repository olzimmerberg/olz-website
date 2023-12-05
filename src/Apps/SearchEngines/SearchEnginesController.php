<?php

namespace Olz\Apps\SearchEngines;

use Olz\Apps\SearchEngines\Components\OlzSearchEngines\OlzSearchEngines;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchEnginesController extends AbstractController {
    #[Route('/apps/search_engines')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzSearchEngines::render([]);
        return new Response($html_out);
    }
}
