<?php

namespace Olz\Controller;

use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Components\OtherPages\OlzTrophy\OlzTrophy;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OtherPagesController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/datenschutz')]
    public function datenschutz(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzDatenschutz::render();
        return new Response($out);
    }

    #[Route('/fuer_einsteiger')]
    public function fuerEinsteiger(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $this->httpUtils()->countRequest($request, ['von']);
        $out = OlzFuerEinsteiger::render();
        return new Response($out);
    }

    #[Route('/material')]
    public function material(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzMaterial::render();
        return new Response($out);
    }

    #[Route('/trophy')]
    public function trophy(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTrophy::render();
        return new Response($out);
    }
}
