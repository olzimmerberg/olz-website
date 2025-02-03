<?php

namespace Olz\Apps\Anmelden;

use Olz\Apps\Anmelden\Components\OlzAnmelden\OlzAnmelden;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnmeldenController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/anmelden')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzAnmelden::render();
        return new Response($html_out);
    }

    #[Route('/apps/anmelden/{id}', requirements: [
        'id' => '[a-zA-Z0-9_-]+',
    ])]
    public function detail(
        Request $request,
        LoggerInterface $logger,
        string $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzAnmelden::render(['id' => $id]);
        return new Response($html_out);
    }
}
