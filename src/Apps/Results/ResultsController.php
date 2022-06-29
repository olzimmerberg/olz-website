<?php

namespace Olz\Apps\Results;

use Olz\Apps\Results\Components\OlzResults\OlzResults;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultsController extends AbstractController {
    #[Route('/apps/resultate')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzResults::render();
        return new Response($html_out);
    }
}
