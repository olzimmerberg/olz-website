<?php

namespace Olz\Apps\Import;

use Olz\Apps\Import\Components\OlzImport\OlzImport;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/import')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzImport::render();
        return new Response($html_out);
    }
}
