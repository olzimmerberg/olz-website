<?php

namespace Olz\Apps\Oev;

use Olz\Apps\Oev\Components\OlzOev\OlzOev;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OevController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/oev')]
    public function index(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzOev::render();
        return new Response($html_out);
    }
}
