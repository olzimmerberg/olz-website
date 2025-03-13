<?php

namespace Olz\Apps\SearchEngines;

use Olz\Apps\SearchEngines\Components\OlzSearchEngines\OlzSearchEngines;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchEnginesController extends AbstractController {
    #[Route('/apps/search_engines')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzSearchEngines $olzSearchEngines,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzSearchEngines->getHtml([]);
        return new Response($html_out);
    }
}
