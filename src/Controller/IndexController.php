<?php

namespace App\Controller;

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
        $request_uri = $request->getRequestUri();
        return new RedirectResponse("/_{$request_uri}", 301);
    }

    #[Route('/_/')]
    public function underscore_index(
        Request $request,
        LoggerInterface $logger,
    ): RedirectResponse {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/_/index.php?{$query_string}" : '/_/index.php';
        return new RedirectResponse($url, 301);
    }

    #[Route('/_/{folder}/')]
    public function underscore_folder_index(
        Request $request,
        LoggerInterface $logger,
        string $folder,
    ): RedirectResponse {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/_/{$folder}/index.php?{$query_string}" : "/_/{$folder}/index.php";
        return new RedirectResponse($url, 301);
    }
}
