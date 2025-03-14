<?php

namespace Olz\Apps\Newsletter;

use Olz\Apps\Newsletter\Components\OlzNewsletter\OlzNewsletter;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController {
    #[Route('/apps/newsletter')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzNewsletter $olzNewsletter,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzNewsletter->getHtml([]);
        return new Response($html_out);
    }
}
