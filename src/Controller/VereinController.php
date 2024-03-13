<?php

namespace Olz\Controller;

use Olz\Roles\Components\OlzRolePage\OlzRolePage;
use Olz\Roles\Components\OlzVerein\OlzVerein;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VereinController extends AbstractController {
    #[Route('/verein')]
    public function verein(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzVerein::render();
        return new Response($out);
    }

    #[Route('/verein/{ressort}')]
    public function ressort(
        Request $request,
        LoggerInterface $logger,
        string $ressort,
    ): Response {
        $out = OlzRolePage::render(['ressort' => $ressort]);
        return new Response($out);
    }
}
