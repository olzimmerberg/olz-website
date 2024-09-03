<?php

namespace Olz\Controller;

use Olz\Entity\Karten\Karte;
use Olz\Karten\Components\OlzKarteDetail\OlzKarteDetail;
use Olz\Karten\Components\OlzKarten\OlzKarten;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KartenController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/karten')]
    public function karten(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzKarten::render([]);
        return new Response($out);
    }

    #[Route('/karten/{ident}')]
    public function karteDetail(
        Request $request,
        LoggerInterface $logger,
        string $ident,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneByIdent($ident);
        $out = OlzKarteDetail::render(['karte' => $karte]);
        return new Response($out);
    }
}
