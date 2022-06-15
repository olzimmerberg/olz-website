<?php

namespace Olz\Controller;

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
        $url = $query_string ? "/startseite.php?{$query_string}" : '/startseite.php';
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'index']);
    }
}
