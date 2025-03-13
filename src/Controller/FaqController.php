<?php

namespace Olz\Controller;

use Olz\Faq\Components\OlzFaqDetail\OlzFaqDetail;
use Olz\Faq\Components\OlzFaqList\OlzFaqList;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController {
    #[Route('/fragen_und_antworten')]
    public function list(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzFaqList $olzFaqList,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $out = $olzFaqList->getHtml([]);
        return new Response($out);
    }

    #[Route('/fragen_und_antworten/{ident}')]
    public function detail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzFaqDetail $olzFaqDetail,
        string $ident,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $out = $olzFaqDetail->getHtml(['ident' => $ident]);
        return new Response($out);
    }
}
