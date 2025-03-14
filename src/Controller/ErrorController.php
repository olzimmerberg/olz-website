<?php

namespace Olz\Controller;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController {
    #[Route('/error/{code}', requirements: ['code' => '[0-9]{3}'])]
    public function index(
        Request $request,
        LoggerInterface $logger,
        OlzErrorPage $olzErrorPage,
        string $code,
    ): Response {
        $out = $olzErrorPage->getHtml(['http_status_code' => intval($code)]);
        return new Response($out);
    }
}
