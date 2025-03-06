<?php

namespace Olz\Controller;

use Olz\Faq\Components\OlzFaqDetail\OlzFaqDetail;
use Olz\Faq\Components\OlzFaqList\OlzFaqList;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/fragen_und_antworten')]
    public function list(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request, ['von']);
        $out = OlzFaqList::render();
        return new Response($out);
    }

    #[Route('/fragen_und_antworten/{ident}')]
    public function detail(
        Request $request,
        LoggerInterface $logger,
        string $ident,
    ): Response {
        $this->httpUtils()->countRequest($request, ['von']);
        $out = OlzFaqDetail::render(['ident' => $ident]);
        return new Response($out);
    }
}
