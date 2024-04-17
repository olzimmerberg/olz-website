<?php

namespace Olz\Apps\Statistics;

use Olz\Apps\Statistics\Components\OlzStatistics\OlzStatistics;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/statistics')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzStatistics::render([]);
        return new Response($html_out);
    }
}
