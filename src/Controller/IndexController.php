<?php

namespace Olz\Controller;

use Olz\Utils\EnvUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {
    #[Route('/')]
    public function index(
        Request $request,
        LoggerInterface $logger,
    ): RedirectResponse {
        $query_string = $request->getQueryString();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $home_url = "{$code_href}startseite.php";
        $url = $query_string ? "{$home_url}?{$query_string}" : $home_url;
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'index']);
    }
}
