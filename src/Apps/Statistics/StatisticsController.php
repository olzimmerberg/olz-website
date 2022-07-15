<?php

namespace Olz\Apps\Statistics;

use Olz\Apps\Statistics\Components\OlzStatistics\OlzStatistics;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController {
    #[Route('/apps/statistics')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzStatistics::render([]);
        return new Response($html_out);
    }
}
