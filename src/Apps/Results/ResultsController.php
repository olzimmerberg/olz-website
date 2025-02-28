<?php

namespace Olz\Apps\Results;

use Olz\Apps\Results\Components\OlzResults\OlzResults;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/resultate')]
    public function index(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzResults::render();
        return new Response($html_out);
    }
}
