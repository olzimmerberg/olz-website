<?php

namespace Olz\Apps\GoogleSearch;

use Olz\Apps\GoogleSearch\Components\OlzGoogleSearch\OlzGoogleSearch;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSearchController extends AbstractController {
    #[Route('/apps/google_search')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzGoogleSearch::render([]);
        return new Response($html_out);
    }
}
