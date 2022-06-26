<?php

namespace Olz\Apps\Anmelden;

use Olz\Apps\Anmelden\Components\OlzAnmelden\OlzAnmelden;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnmeldenController extends AbstractController {
    #[Route('/apps/anmelden')]
    public function webdavIndex(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzAnmelden::render();
        return new Response($html_out);
    }
}
