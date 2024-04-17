<?php

namespace Olz\Controller;

use Olz\Karten\Components\OlzKarten\OlzKarten;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KartenController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/karten')]
    public function karten(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzKarten::render([]);
        return new Response($out);
    }
}
