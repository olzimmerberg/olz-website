<?php

namespace Olz\Apps\Newsletter;

use Olz\Apps\Newsletter\Components\OlzNewsletter\OlzNewsletter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController {
    #[Route('/apps/newsletter')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzNewsletter::render();
        return new Response($html_out);
    }
}
