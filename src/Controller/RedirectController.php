<?php

namespace Olz\Controller;

use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController {
    #[Route('/_/{file}.php')]
    public function php(Request $request, HttpUtils $http_utils, string $file): Response {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/{$file}.php?{$query_string}" : "/{$file}.php";
        $http_utils->redirect($url, 410);
        return new RedirectResponse($url, 308);
    }

    #[Route('/{file}.php/index.php')]
    public function phpPhp(Request $request, HttpUtils $http_utils, string $file): Response {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/{$file}.php?{$query_string}" : "/{$file}.php";
        $http_utils->redirect($url, 410);
        return new RedirectResponse($url, 308);
    }

    #[Route('/_/{file}.php/index.php')]
    public function underscorePhpPhp(Request $request, HttpUtils $http_utils, string $file): Response {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/{$file}.php?{$query_string}" : "/{$file}.php";
        $http_utils->redirect($url, 410);
        return new RedirectResponse($url, 308);
    }

    #[Route('/_/')]
    public function underscoreIndex(
        Request $request,
        HttpUtils $http_utils,
        LoggerInterface $logger,
    ): Response {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/?{$query_string}" : '/';
        $http_utils->redirect($url, 410);
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_index']);
    }

    #[Route('/_/{folder}/', requirements: ['folder' => '[^\.]+'])]
    public function underscoreFolderIndex(
        Request $request,
        HttpUtils $http_utils,
        LoggerInterface $logger,
        string $folder,
    ): Response {
        $query_string = $request->getQueryString();
        $html_exists = is_file("./_/{$folder}/index.html");
        if ($html_exists) {
            $url = $query_string ? "/{$folder}/index.html?{$query_string}" : "/{$folder}/index.html";
            return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_folder_index']);
        }
        $url = $query_string ? "/{$folder}/index.php?{$query_string}" : "/{$folder}/index.php";
        $http_utils->redirect($url, 410);
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'underscore_folder_index']);
    }
}
