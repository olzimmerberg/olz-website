<?php

namespace Olz\Apps\Anmelden;

use Olz\Apps\Anmelden\Components\OlzAnmelden\OlzAnmelden;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnmeldenController extends AbstractController {
    #[Route('/apps/anmelden')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzAnmelden $olzAnmelden,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzAnmelden->getHtml([]);
        return new Response($html_out);
    }

    #[Route('/apps/anmelden/{id}', requirements: [
        'id' => '[a-zA-Z0-9_-]+',
    ])]
    public function detail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzAnmelden $olzAnmelden,
        string $id,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzAnmelden->getHtml(['id' => $id ?: null]);
        return new Response($html_out);
    }
}
