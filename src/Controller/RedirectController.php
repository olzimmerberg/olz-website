<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController {
    #[Route('/_/{file}.php')]
    public function get(Request $request, string $file): RedirectResponse {
        $query_string = $request->getQueryString();
        $url = $query_string ? "/{$file}.php?{$query_string}" : "/{$file}.php";
        return new RedirectResponse($url, 308);
    }
}