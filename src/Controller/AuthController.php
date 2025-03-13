<?php

namespace Olz\Controller;

use Olz\Components\Auth\OlzEmailReaktion\OlzEmailReaktion;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController {
    #[Route('/email_reaktion')]
    public function emailReaktion(
        Request $request,
        HttpUtils $httpUtils,
        OlzEmailReaktion $olzEmailReaktion,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzEmailReaktion->getHtml([]);
        return new Response($out);
    }

    #[Route('/profil')]
    public function profil(
        Request $request,
        HttpUtils $httpUtils,
        LoggerInterface $logger,
    ): Response {
        $httpUtils->countRequest($request);
        $envUtils = EnvUtils::fromEnv();
        $code_href = $envUtils->getCodeHref();
        return new RedirectResponse("{$code_href}benutzer/ich");
    }
}
