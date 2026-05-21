<?php

namespace Olz\Controller;

use Olz\Utils\HttpUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LatencyTestController extends AbstractController {
    #[Route('/latency/simple')]
    public function simple(): Response {
        // Prod: 359, 346, 419, 322, 352, 309, 471, 422, 365, 322 => 368.7 +/- 52.3
        return new Response('simple response');
    }

    #[Route('/latency/counter')]
    public function counter(
        Request $request,
        HttpUtils $httpUtils,
    ): Response {
        // Prod: 382, 530, 439, 425, 844, 529, 431, 522, 516, 713 => 533.1 +/- 142.7
        $httpUtils->countRequest($request, ['von']);
        return new Response('counter response');
    }
}
