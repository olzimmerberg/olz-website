<?php

namespace Olz\Controller;

use Olz\Suche\Components\OlzSuche\OlzSuche;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SucheController extends AbstractController {
    #[Route('/suche')]
    public function suche(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzSuche::render([]);
        return new Response($out);
    }
}
