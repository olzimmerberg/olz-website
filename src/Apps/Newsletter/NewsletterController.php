<?php

namespace Olz\Apps\Newsletter;

use Olz\Apps\Newsletter\Components\OlzNewsletter\OlzNewsletter;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/newsletter')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzNewsletter::render();
        return new Response($html_out);
    }
}
