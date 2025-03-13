<?php

namespace Olz\Controller;

use Olz\Karten\Components\OlzKarteDetail\OlzKarteDetail;
use Olz\Karten\Components\OlzKarten\OlzKarten;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KartenController extends AbstractController {
    #[Route('/karten')]
    public function karten(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzKarten $olzKarten,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzKarten->getHtml([]);
        return new Response($out);
    }

    #[Route('/karten/{id}', requirements: ['id' => '\d+'])]
    public function karteDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzKarteDetail $olzKarteDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzKarteDetail->getHtml(['id' => $id]);
        return new Response($out);
    }
}
