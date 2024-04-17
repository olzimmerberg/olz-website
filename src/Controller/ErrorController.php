<?php

namespace Olz\Controller;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/error/{code}', requirements: ['code' => '[0-9]{3}'])]
    public function index(
        Request $request,
        LoggerInterface $logger,
        string $code,
    ): Response {
        $out = OlzErrorPage::render(['http_status_code' => intval($code)]);
        return new Response($out);
    }
}
