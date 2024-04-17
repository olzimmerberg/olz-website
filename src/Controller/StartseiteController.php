<?php

namespace Olz\Controller;

use Olz\Startseite\Components\OlzStartseite\OlzStartseite;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartseiteController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/')]
    public function startseite(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzStartseite::render([]);
        return new Response($out);
    }
}
