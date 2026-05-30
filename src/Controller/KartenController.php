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
        return $httpUtils->measure($request, ['von'], function () use ($httpUtils, $request, $olzKarten) {
            $httpUtils->stripParams($request, ['von']);
            $out = $olzKarten->getHtml([]);
            return new Response($out);
        });
    }

    #[Route('/karten/{id}', requirements: ['id' => '\d+'])]
    public function karteDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzKarteDetail $olzKarteDetail,
        int $id,
    ): Response {
        return $httpUtils->measure($request, [], function () use ($olzKarteDetail, $id) {
            $out = $olzKarteDetail->getHtml(['id' => $id]);
            return new Response($out);
        });
    }
}
