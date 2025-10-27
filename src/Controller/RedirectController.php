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
}
