<?php

namespace Olz\Controller;

use Olz\Components\OtherPages\OlzAnniversary\OlzAnniversary;
use Olz\Components\OtherPages\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Termine\Utils\TermineUtils;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OtherPagesController extends AbstractController {
    #[Route('/datenschutz')]
    public function datenschutz(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzDatenschutz $olzDatenschutz,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzDatenschutz->getHtml([]);
        return new Response($out);
    }

    #[Route('/fuer_einsteiger')]
    public function fuerEinsteiger(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzFuerEinsteiger $olzFuerEinsteiger,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $httpUtils->stripParams($request, ['von']);
        $out = $olzFuerEinsteiger->getHtml([]);
        return new Response($out);
    }

    #[Route('/material')]
    public function material(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzMaterial $olzMaterial,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzMaterial->getHtml([]);
        return new Response($out);
    }

    #[Route('/2026')]
    public function anniversary(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzAnniversary $olzAnniversary,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzAnniversary->getHtml([]);
        return new Response($out);
    }

    #[Route('/2026/rakete.svg')]
    public function rakete(
        OlzAnniversaryRocket $olzAnniversaryRocket,
    ): Response {
        $out = $olzAnniversaryRocket->getHtml([]);
        return new Response($out);
    }

    #[Route('/trophy')]
    public function trophy(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        EnvUtils $envUtils,
        TermineUtils $termineUtils,
    ): Response {
        $httpUtils->countRequest($request);
        $dateUtils = new DateUtils();
        $code_href = $envUtils->getCodeHref();
        $this_year = $dateUtils->getCurrentDateInFormat('Y');
        $filter = [
            ...$termineUtils->getDefaultFilter(),
            'typ' => 'trophy',
            'datum' => strval($this_year),
        ];
        $serialized_filter = $termineUtils->serialize($filter);
        $url = "{$code_href}termine?filter={$serialized_filter}";
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'trophy']);
    }
}
