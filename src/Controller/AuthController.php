<?php

namespace Olz\Controller;

use Olz\Components\Auth\OlzEmailReaktion\OlzEmailReaktion;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController {
    #[Route('/email_reaktion')]
    public function emailReaktion(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzEmailReaktion::render();
        return new Response($out);
    }
}
