<?php

namespace Olz\Controller;

use Olz\Suche\Components\OlzSuche\OlzSuche;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SucheController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/suche')]
    public function suche(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzSuche::render([]);
        return new Response($out);
    }
}
