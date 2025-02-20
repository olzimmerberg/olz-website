<?php

namespace Olz\Controller;

use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $code_href = $this->envUtils()->getCodeHref();
        $this_year = $this->dateUtils()->getCurrentDateInFormat('Y');
        $termine_utils = TermineFilterUtils::fromEnv();
        $filter = [
            ...$termine_utils->getDefaultFilter(),
            'typ' => 'trophy',
            'datum' => strval($this_year),
        ];
        $enc_filter = urlencode(json_encode($filter) ?: '');
        $url = "{$code_href}termine?filter={$enc_filter}";
        return new RedirectResponse($url, 301, ['X-OLZ-Redirect' => 'trophy']);
    }
}
