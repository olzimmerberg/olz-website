<?php

namespace Olz\Controller;

use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFragenUndAntworten\OlzFragenUndAntworten;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Components\OtherPages\OlzTrophy\OlzTrophy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OtherPagesController extends AbstractController {
    #[Route('/datenschutz')]
    public function datenschutz(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzDatenschutz::render();
        return new Response($out);
    }

    #[Route('/fragen_und_antworten')]
    public function fragenUndAntworten(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzFragenUndAntworten::render();
        return new Response($out);
    }

    #[Route('/fuer_einsteiger')]
    public function fuerEinsteiger(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzFuerEinsteiger::render();
        return new Response($out);
    }

    #[Route('/material')]
    public function material(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzMaterial::render();
        return new Response($out);
    }

    #[Route('/trophy')]
    public function trophy(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzTrophy::render();
        return new Response($out);
    }
}
