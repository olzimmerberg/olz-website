<?php

namespace Olz\Controller;

use Olz\Faq\Components\OlzFaq\OlzFaq;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/fragen_und_antworten')]
    public function fragenUndAntworten(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzFaq::render();
        return new Response($out);
    }
}
