<?php

namespace Olz\Apps\Import;

use Olz\Apps\Import\Components\OlzImport\OlzImport;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController {
    #[Route('/apps/import')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzImport::render();
        return new Response($html_out);
    }
}
