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
        return new RedirectResponse("/startseite.php", 301, ['X-OLZ-Redirect' => 'index']);
    }

    #[Route('/_/')]
    public function underscore_index(
        Request $request,
        LoggerInterface $logger,
    ): RedirectResponse {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/index.php?{$query_string}" : '/index.php';
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_index']);
    }

    #[Route('/_/{folder}/')]
    public function underscore_folder_index(
        Request $request,
        LoggerInterface $logger,
        string $folder,
    ): RedirectResponse {
        $query_string = $request->getQueryString();
        $html_exists = is_file("./_/{$folder}/index.html");
        if ($html_exists) {
            $url = $query_string ? "/{$folder}/index.html?{$query_string}" : "/{$folder}/index.html";
            return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_folder_index']);
        }
        $url = $query_string ? "/{$folder}/index.php?{$query_string}" : "/{$folder}/index.php";
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_folder_index']);
    }
}
