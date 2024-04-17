<?php

namespace Olz\Apps\SearchEngines;

use Olz\Apps\SearchEngines\Components\OlzSearchEngines\OlzSearchEngines;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchEnginesController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/search_engines')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzSearchEngines::render([]);
        return new Response($html_out);
    }
}
